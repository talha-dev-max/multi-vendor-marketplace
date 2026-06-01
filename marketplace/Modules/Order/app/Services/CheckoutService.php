<?php

declare(strict_types=1);

namespace Modules\Order\Services;

use Modules\Cart\Repositories\Contracts\CartRepositoryInterface;
use Modules\Catalog\Models\Product;
use Modules\Catalog\Repositories\Contracts\ProductRepositoryInterface;
use Modules\Order\DTOs\CheckoutResultDto;
use Modules\Order\DTOs\PlaceOrderDto;
use Modules\Order\Exceptions\EmptyCartException;
use Modules\Order\Exceptions\InsufficientStockException;
use Modules\Order\Models\Order;
use Modules\Earning\Services\EarningService;
use Modules\Order\Repositories\Contracts\OrderItemRepositoryInterface;
use Modules\Order\Repositories\Contracts\OrderRepositoryInterface;
use Modules\Order\Repositories\Contracts\VendorOrderRepositoryInterface;
use Modules\Payment\Services\PaymentService;

/**
 * Owns the full checkout business logic. Called by CheckoutManager inside DB::transaction.
 *
 * This is the ONLY place where checkout persistence flows live — the Manager is just
 * a transaction wrapper that calls into this service.
 */
class CheckoutService
{
    public function __construct(
        private readonly CartRepositoryInterface $carts,
        private readonly ProductRepositoryInterface $products,
        private readonly OrderRepositoryInterface $orders,
        private readonly VendorOrderRepositoryInterface $vendorOrders,
        private readonly OrderItemRepositoryInterface $orderItems,
        private readonly PaymentService $paymentService,
        private readonly EarningService $earningService,
    ) {
    }

    public function execute(PlaceOrderDto $dto): CheckoutResultDto
    {
        // 1. Load cart with items + products
        $cart = $this->carts->getOrCreateForUser($dto->userId);
        $cart = $this->carts->loadWithItems($cart);

        if ($cart->items->isEmpty()) {
            throw new EmptyCartException();
        }

        // 2. SELECT ... FOR UPDATE the products (row-level lock)
        $productIds = $cart->items->pluck('product_id')->unique()->map(fn ($id) => (int) $id)->all();
        $lockedProducts = $this->products->lockForUpdateByIds($productIds)->keyBy('id');

        // 3. Validate stock on the locked snapshot
        foreach ($cart->items as $item) {
            $product = $lockedProducts->get($item->product_id);

            if ($product === null || $product->status !== Product::STATUS_ACTIVE) {
                throw new InsufficientStockException("Product {$item->product_id} is no longer available.");
            }

            if ($product->stock < $item->quantity) {
                throw new InsufficientStockException(
                    "Not enough stock for product [{$product->name}]. Requested {$item->quantity}, available {$product->stock}.",
                );
            }
        }

        // 4. Group by vendor_id
        $itemsByVendor = $cart->items->groupBy(fn ($item) => $item->product->vendor_id);

        // 5. Compute grand total
        $grandTotal = (float) $cart->items->sum(fn ($item) => (float) $item->unit_price_snapshot * $item->quantity);

        // 6. Create parent order
        $order = $this->orders->create(
            customerId: $dto->userId,
            total: $grandTotal,
            currency: (string) config('marketplace.currency', 'usd'),
            paymentMethod: $dto->paymentMethod,
            shippingAddress: $dto->shippingAddress->toArray(),
        );

        $commissionRate = (float) config('marketplace.commission_rate', 0.15);

        // 7. For each vendor group: vendor_order + order_items + stock decrement
        foreach ($itemsByVendor as $vendorId => $vendorItems) {
            $subtotal = (float) $vendorItems->sum(fn ($item) => (float) $item->unit_price_snapshot * $item->quantity);
            $commission = round($subtotal * $commissionRate, 2);
            $net = round($subtotal - $commission, 2);

            $vendorOrder = $this->vendorOrders->create(
                order: $order,
                vendorId: (int) $vendorId,
                subtotal: $subtotal,
                commission: $commission,
                net: $net,
            );

            foreach ($vendorItems as $item) {
                $product = $lockedProducts->get($item->product_id);
                $unitPrice = (float) $item->unit_price_snapshot;
                $lineTotal = $unitPrice * $item->quantity;

                $this->orderItems->create(
                    vendorOrder: $vendorOrder,
                    productId: (int) $product->id,
                    productName: (string) $product->name,
                    unitPrice: $unitPrice,
                    quantity: (int) $item->quantity,
                    lineTotal: $lineTotal,
                );

                $this->products->decrementStock($product, (int) $item->quantity);
            }

            // Seed the earning record for this vendor_order (status=pending)
            $this->earningService->recordForVendorOrder($vendorOrder);
        }

        // 8. Clear the cart
        $this->carts->clear($cart);

        // 9. Payment record (COD or Stripe)
        $stripeCheckoutUrl = null;
        if ($dto->paymentMethod === Order::PAYMENT_COD) {
            $this->paymentService->recordCodPayment($order);
        } else {
            $result = $this->paymentService->startStripeCheckout(
                order: $order->fresh(['customer']),
                successUrl: (string) $dto->stripeSuccessUrl,
                cancelUrl: (string) $dto->stripeCancelUrl,
            );
            $stripeCheckoutUrl = $result->checkoutUrl;
        }

        return new CheckoutResultDto(
            order: $order->fresh(['vendorOrders.items.product', 'vendorOrders.vendor']),
            stripeCheckoutUrl: $stripeCheckoutUrl,
        );
    }
}
