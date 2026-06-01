<?php

declare(strict_types=1);

namespace Modules\Cart\Managers;

use Illuminate\Support\Facades\DB;
use Modules\Cart\DTOs\AddToCartDto;
use Modules\Cart\DTOs\UpdateCartItemDto;
use Modules\Cart\Models\Cart;
use Modules\Cart\Services\CartService;

class CartManager
{
    public function __construct(
        private readonly CartService $cartService,
    ) {
    }

    public function getForUser(int $userId): Cart
    {
        return $this->cartService->getForUser($userId);
    }

    public function addItem(AddToCartDto $dto): Cart
    {
        return DB::transaction(fn () => $this->cartService->addItem($dto));
    }

    public function updateItem(UpdateCartItemDto $dto): Cart
    {
        return DB::transaction(fn () => $this->cartService->updateItem($dto));
    }

    public function removeItem(int $cartItemId, int $userId): Cart
    {
        return DB::transaction(fn () => $this->cartService->removeItem($cartItemId, $userId));
    }

    public function clear(int $userId): Cart
    {
        return DB::transaction(fn () => $this->cartService->clear($userId));
    }
}
