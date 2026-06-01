<?php

declare(strict_types=1);

namespace Modules\Cart\Repositories;

use Modules\Cart\Models\Cart;
use Modules\Cart\Models\CartItem;
use Modules\Cart\Repositories\Contracts\CartRepositoryInterface;

class CartRepository implements CartRepositoryInterface
{
    public function getOrCreateForUser(int $userId): Cart
    {
        return Cart::query()->firstOrCreate(['user_id' => $userId]);
    }

    public function loadWithItems(Cart $cart): Cart
    {
        return $cart->load('items.product.images', 'items.product.vendor');
    }

    public function findItemInCart(Cart $cart, int $productId): ?CartItem
    {
        return CartItem::query()
            ->where('cart_id', $cart->id)
            ->where('product_id', $productId)
            ->first();
    }

    public function findItemByIdForUser(int $cartItemId, int $userId): ?CartItem
    {
        return CartItem::query()
            ->where('id', $cartItemId)
            ->whereHas('cart', fn ($q) => $q->where('user_id', $userId))
            ->first();
    }

    public function addItem(Cart $cart, int $productId, int $quantity, float $unitPriceSnapshot): CartItem
    {
        return CartItem::query()->create([
            'cart_id' => $cart->id,
            'product_id' => $productId,
            'quantity' => $quantity,
            'unit_price_snapshot' => $unitPriceSnapshot,
        ]);
    }

    public function updateItemQuantity(CartItem $item, int $quantity): CartItem
    {
        $item->fill(['quantity' => $quantity])->save();

        return $item->fresh();
    }

    public function removeItem(CartItem $item): bool
    {
        return (bool) $item->delete();
    }

    public function clear(Cart $cart): void
    {
        CartItem::query()->where('cart_id', $cart->id)->delete();
    }
}
