<?php

declare(strict_types=1);

namespace Modules\Earning\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Earning\Http\Resources\VendorEarningResource;
use Modules\Earning\Managers\EarningManager;
use Modules\Vendor\Models\VendorProfile;
use Throwable;

class VendorEarningController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly EarningManager $earningManager,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $vendor = $this->vendorProfile($request);
            $paginator = $this->earningManager->paginateForVendor(
                $vendor->id,
                (int) $request->query('per_page', 15),
            );

            return $this->success([
                'items' => VendorEarningResource::collection($paginator->items()),
                'meta' => [
                    'current_page' => $paginator->currentPage(),
                    'last_page' => $paginator->lastPage(),
                    'per_page' => $paginator->perPage(),
                    'total' => $paginator->total(),
                ],
            ]);
        } catch (Throwable $e) {
            Log::error('List vendor earnings failed', ['exception' => $e]);

            return $this->error('Unable to fetch earnings.', 500);
        }
    }

    public function summary(Request $request): JsonResponse
    {
        try {
            $vendor = $this->vendorProfile($request);

            return $this->success($this->earningManager->summaryForVendor($vendor->id));
        } catch (Throwable $e) {
            Log::error('Vendor earnings summary failed', ['exception' => $e]);

            return $this->error('Unable to fetch summary.', 500);
        }
    }

    private function vendorProfile(Request $request): VendorProfile
    {
        return VendorProfile::query()->where('user_id', $request->user()->id)->firstOrFail();
    }
}
