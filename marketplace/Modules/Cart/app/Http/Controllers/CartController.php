<?php

declare(strict_types=1);

namespace Modules\Cart\Http\Controllers;

use App\Exceptions\Domain\DomainException;
use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Cart\Http\Requests\AddToCartRequest;
use Modules\Cart\Http\Requests\UpdateCartItemRequest;
use Modules\Cart\Http\Resources\CartResource;
use Modules\Cart\Managers\CartManager;
use Throwable;

class CartController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly CartManager $cartManager,
    ) {
    }

    public function show(Request $request): JsonResponse
    {
        try {
            $cart = $this->cartManager->getForUser((int) $request->user()->id);

            return $this->success(CartResource::make($cart));
        } catch (Throwable $e) {
            Log::error('Show cart failed', ['exception' => $e]);

            return $this->error('Unable to fetch cart.', 500);
        }
    }

    public function addItem(AddToCartRequest $request): JsonResponse
    {
        try {
            $cart = $this->cartManager->addItem($request->toDto());

            return $this->success(CartResource::make($cart), 'Item added to cart.');
        } catch (DomainException $e) {
            return $this->error($e->getMessage(), $e->httpStatus());
        } catch (Throwable $e) {
            Log::error('Add to cart failed', ['exception' => $e]);

            return $this->error('Unable to add item to cart.', 500);
        }
    }

    public function updateItem(UpdateCartItemRequest $request, int $id): JsonResponse
    {
        try {
            $cart = $this->cartManager->updateItem($request->toDto($id));

            return $this->success(CartResource::make($cart), 'Cart item updated.');
        } catch (DomainException $e) {
            return $this->error($e->getMessage(), $e->httpStatus());
        } catch (Throwable $e) {
            Log::error('Update cart item failed', ['exception' => $e, 'cart_item_id' => $id]);

            return $this->error('Unable to update cart item.', 500);
        }
    }

    public function removeItem(Request $request, int $id): JsonResponse
    {
        try {
            $cart = $this->cartManager->removeItem($id, (int) $request->user()->id);

            return $this->success(CartResource::make($cart), 'Cart item removed.');
        } catch (DomainException $e) {
            return $this->error($e->getMessage(), $e->httpStatus());
        } catch (Throwable $e) {
            Log::error('Remove cart item failed', ['exception' => $e, 'cart_item_id' => $id]);

            return $this->error('Unable to remove cart item.', 500);
        }
    }

    public function clear(Request $request): JsonResponse
    {
        try {
            $cart = $this->cartManager->clear((int) $request->user()->id);

            return $this->success(CartResource::make($cart), 'Cart cleared.');
        } catch (Throwable $e) {
            Log::error('Clear cart failed', ['exception' => $e]);

            return $this->error('Unable to clear cart.', 500);
        }
    }
}
