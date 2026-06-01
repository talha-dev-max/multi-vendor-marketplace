<?php

declare(strict_types=1);

namespace Modules\Order\Http\Controllers\Admin;

use App\Exceptions\Domain\DomainException;
use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Order\Http\Resources\OrderResource;
use Modules\Order\Managers\OrderManager;
use Throwable;

class OrderController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly OrderManager $orderManager,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $paginator = $this->orderManager->paginateAll((int) $request->query('per_page', 15));

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
            Log::error('Admin list orders failed', ['exception' => $e]);

            return $this->error('Unable to fetch orders.', 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $order = $this->orderManager->findById($id);

            return $this->success(OrderResource::make($order));
        } catch (DomainException $e) {
            return $this->error($e->getMessage(), $e->httpStatus());
        } catch (Throwable $e) {
            Log::error('Admin show order failed', ['exception' => $e]);

            return $this->error('Unable to fetch order.', 500);
        }
    }
}
