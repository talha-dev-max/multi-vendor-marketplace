<?php

declare(strict_types=1);

namespace Modules\Order\Http\Controllers\Customer;

use App\Exceptions\Domain\DomainException;
use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Order\Http\Requests\PlaceOrderRequest;
use Modules\Order\Http\Resources\OrderResource;
use Modules\Order\Managers\CheckoutManager;
use Modules\Order\Managers\OrderManager;
use Throwable;

class OrderController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly CheckoutManager $checkoutManager,
        private readonly OrderManager $orderManager,
    ) {
    }

    public function place(PlaceOrderRequest $request): JsonResponse
    {
        try {
            $result = $this->checkoutManager->placeOrder($request->toDto());

            return $this->success([
                'order' => OrderResource::make($result->order),
                'stripe_checkout_url' => $result->stripeCheckoutUrl,
            ], 'Order placed.', 201);
        } catch (DomainException $e) {
            return $this->error($e->getMessage(), $e->httpStatus());
        } catch (Throwable $e) {
            Log::error('Place order failed', ['exception' => $e]);

            return $this->error('Unable to place order.', 500);
        }
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $paginator = $this->orderManager->paginateForCustomer(
                (int) $request->user()->id,
                (int) $request->query('per_page', 15),
            );

            return $this->success([
                'items' => OrderResource::collection($paginator->items()),
                'meta' => [
                    'current_page' => $paginator->currentPage(),
                    'last_page' => $paginator->lastPage(),
                    'per_page' => $paginator->perPage(),
                    'total' => $paginator->total(),
                ],
            ]);
        } catch (Throwable $e) {
            Log::error('List customer orders failed', ['exception' => $e]);

            return $this->error('Unable to fetch orders.', 500);
        }
    }

    public function show(Request $request, int $id): JsonResponse
    {
        try {
            $order = $this->orderManager->findForCustomer($id, (int) $request->user()->id);

            return $this->success(OrderResource::make($order));
        } catch (DomainException $e) {
            return $this->error($e->getMessage(), $e->httpStatus());
        } catch (Throwable $e) {
            Log::error('Show customer order failed', ['exception' => $e]);

            return $this->error('Unable to fetch order.', 500);
        }
    }
}
