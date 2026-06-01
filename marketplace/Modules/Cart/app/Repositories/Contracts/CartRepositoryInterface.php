<?php

declare(strict_types=1);

namespace Modules\Cart\Repositories\Contracts;

use Modules\Cart\Models\Cart;
use Modules\Cart\Models\CartItem;

interface CartRepositoryInterface
{
    public function getOrCreateForUser(int $userId): Cart;

    public function loadWithItems(Cart $cart): Cart;

    public function findItemInCart(Cart $cart, int $productId): ?CartItem;

    public function findItemByIdForUser(int $cartItemId, int $userId): ?CartItem;

    public function addItem(Cart $cart, int $productId, int $quantity, float $unitPriceSnapshot): CartItem;

    public function updateItemQuantity(CartItem $item, int $quantity): CartItem;

    public function removeItem(CartItem $item): bool;

    public function clear(Cart $cart): void;
}
