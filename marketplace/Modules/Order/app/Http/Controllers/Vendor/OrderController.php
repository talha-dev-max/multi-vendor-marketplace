<?php

declare(strict_types=1);

namespace Modules\Order\Http\Controllers\Vendor;

use App\Exceptions\Domain\DomainException;
use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Order\Http\Requests\UpdateVendorOrderStatusRequest;
use Modules\Order\Http\Resources\VendorOrderResource;
use Modules\Order\Managers\VendorOrderManager;
use Modules\Vendor\Models\VendorProfile;
use Throwable;

class OrderController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly VendorOrderManager $vendorOrderManager,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $vendor = $this->vendorProfile($request);
            $paginator = $this->vendorOrderManager->paginateForVendor(
                $vendor->id,
                (int) $request->query('per_page', 15),
            );

            return $this->success([
                'items' => VendorOrderResource::collection($paginator->items()),
                'meta' => [
                    'current_page' => $paginator->currentPage(),
                    'last_page' => $paginator->lastPage(),
                    'per_page' => $paginator->perPage(),
                    'total' => $paginator->total(),
                ],
            ]);
        } catch (Throwable $e) {
            Log::error('List vendor orders failed', ['exception' => $e]);

            return $this->error('Unable to fetch orders.', 500);
        }
    }

    public function show(Request $request, int $id): JsonResponse
    {
        try {
            $vendor = $this->vendorProfile($request);
            $vendorOrder = $this->vendorOrderManager->findForVendor($id, $vendor->id);

            return $this->success(VendorOrderResource::make($vendorOrder));
        } catch (DomainException $e) {
            return $this->error($e->getMessage(), $e->httpStatus());
        } catch (Throwable $e) {
            Log::error('Show vendor order failed', ['exception' => $e]);

            return $this->error('Unable to fetch order.', 500);
        }
    }

    public function updateStatus(UpdateVendorOrderStatusRequest $request, int $id): JsonResponse
    {
        try {
            $vendorOrder = $this->vendorOrderManager->updateStatus($request->toDto($id));

            return $this->success(VendorOrderResource::make($vendorOrder), 'Status updated.');
        } catch (DomainException $e) {
            return $this->error($e->getMessage(), $e->httpStatus());
        } catch (Throwable $e) {
            Log::error('Update vendor order status failed', ['exception' => $e]);

            return $this->error('Unable to update status.', 500);
        }
    }

    private function vendorProfile(Request $request): VendorProfile
    {
        return VendorProfile::query()->where('user_id', $request->user()->id)->firstOrFail();
    }
}
