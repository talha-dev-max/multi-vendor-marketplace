<?php

declare(strict_types=1);

namespace Modules\Cart\Services;

use Modules\Cart\DTOs\AddToCartDto;
use Modules\Cart\DTOs\UpdateCartItemDto;
use Modules\Cart\Exceptions\CartItemNotFoundException;
use Modules\Cart\Exceptions\ProductNotAvailableException;
use Modules\Cart\Models\Cart;
use Modules\Cart\Models\CartItem;
use Modules\Cart\Repositories\Contracts\CartRepositoryInterface;
use Modules\Catalog\Models\Product;
use Modules\Catalog\Repositories\Contracts\ProductRepositoryInterface;

class CartService
{
    public function __construct(
        private readonly CartRepositoryInterface $carts,
        private readonly ProductRepositoryInterface $products,
    ) {
    }

    public function getForUser(int $userId): Cart
    {
        $cart = $this->carts->getOrCreateForUser($userId);

        return $this->carts->loadWithItems($cart);
    }

    public function addItem(AddToCartDto $dto): Cart
    {
        $product = $this->products->findById($dto->productId);

        if ($product === null || $product->status !== Product::STATUS_ACTIVE) {
            throw new ProductNotAvailableException();
        }

        if ($product->stock < $dto->quantity) {
            throw new ProductNotAvailableException('Not enough stock.');
        }

        $cart = $this->carts->getOrCreateForUser($dto->userId);
        $existing = $this->carts->findItemInCart($cart, $dto->productId);

        if ($existing !== null) {
            $this->carts->updateItemQuantity($existing, $existing->quantity + $dto->quantity);
        } else {
            $this->carts->addItem($cart, $dto->productId, $dto->quantity, (float) $product->price);
        }

        return $this->carts->loadWithItems($cart);
    }

    public function updateItem(UpdateCartItemDto $dto): Cart
    {
        $item = $this->carts->findItemByIdForUser($dto->cartItemId, $dto->userId);

        if ($item === null) {
            throw new CartItemNotFoundException();
        }

        $this->carts->updateItemQuantity($item, $dto->quantity);

        return $this->carts->loadWithItems($item->cart);
    }

    public function removeItem(int $cartItemId, int $userId): Cart
    {
        $item = $this->carts->findItemByIdForUser($cartItemId, $userId);

        if ($item === null) {
            throw new CartItemNotFoundException();
        }

        $cart = $item->cart;
        $this->carts->removeItem($item);

        return $this->carts->loadWithItems($cart);
    }

    public function clear(int $userId): Cart
    {
        $cart = $this->carts->getOrCreateForUser($userId);
        $this->carts->clear($cart);

        return $this->carts->loadWithItems($cart);
    }
}
