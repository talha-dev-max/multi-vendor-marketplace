<?php

declare(strict_types=1);

namespace Modules\Vendor\Managers;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Modules\Vendor\DTOs\RejectVendorDto;
use Modules\Vendor\Models\VendorProfile;
use Modules\Vendor\Services\VendorApprovalService;

class VendorApprovalManager
{
    public function __construct(
        private readonly VendorApprovalService $approvalService,
    ) {
    }

    public function listByStatus(?string $status, int $perPage = 15): LengthAwarePaginator
    {
        return $this->approvalService->listByStatus($status, $perPage);
    }

    public function approve(int $profileId): VendorProfile
    {
        return DB::transaction(function () use ($profileId): VendorProfile {
            $profile = $this->approvalService->findByIdOrFail($profileId);
            $profile = $this->approvalService->approve($profile);

            // Role swap via spatie: assign vendor role to the user
            $profile->user->assignRole('vendor');

            return $profile->fresh('user');
        });
    }

    public function reject(int $profileId, RejectVendorDto $dto): VendorProfile
    {
        return DB::transaction(function () use ($profileId, $dto): VendorProfile {
            $profile = $this->approvalService->findByIdOrFail($profileId);

            return $this->approvalService->reject($profile, $dto->reason);
        });
    }
}
