<?php

declare(strict_types=1);

namespace Modules\Vendor\Http\Controllers\Admin;

use App\Exceptions\Domain\DomainException;
use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Vendor\Http\Requests\RejectVendorRequest;
use Modules\Vendor\Http\Resources\VendorProfileResource;
use Modules\Vendor\Managers\VendorApprovalManager;
use Throwable;

class VendorApprovalController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly VendorApprovalManager $approvalManager,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $paginator = $this->approvalManager->listByStatus(
                status: $request->query('status'),
                perPage: (int) $request->query('per_page', 15),
            );

            return $this->success([
                'items' => VendorProfileResource::collection($paginator->items()),
                'meta' => [
                    'current_page' => $paginator->currentPage(),
                    'last_page' => $paginator->lastPage(),
                    'per_page' => $paginator->perPage(),
                    'total' => $paginator->total(),
                ],
            ]);
        } catch (Throwable $e) {
            Log::error('List vendor applications failed', ['exception' => $e]);

            return $this->error('Unable to fetch applications.', 500);
        }
    }

    public function approve(int $id): JsonResponse
    {
        try {
            $profile = $this->approvalManager->approve($id);

            return $this->success(VendorProfileResource::make($profile), 'Vendor approved.');
        } catch (DomainException $e) {
            return $this->error($e->getMessage(), $e->httpStatus());
        } catch (Throwable $e) {
            Log::error('Approve vendor failed', ['exception' => $e, 'profile_id' => $id]);

            return $this->error('Unable to approve vendor.', 500);
        }
    }

    public function reject(RejectVendorRequest $request, int $id): JsonResponse
    {
        try {
            $profile = $this->approvalManager->reject($id, $request->toDto());

            return $this->success(VendorProfileResource::make($profile), 'Vendor rejected.');
        } catch (DomainException $e) {
            return $this->error($e->getMessage(), $e->httpStatus());
        } catch (Throwable $e) {
            Log::error('Reject vendor failed', ['exception' => $e, 'profile_id' => $id]);

            return $this->error('Unable to reject vendor.', 500);
        }
    }
}
