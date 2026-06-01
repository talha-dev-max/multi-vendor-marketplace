<?php

declare(strict_types=1);

namespace Modules\Vendor\Managers;

use Illuminate\Support\Facades\DB;
use Modules\Vendor\DTOs\UpdateVendorProfileDto;
use Modules\Vendor\DTOs\VendorApplicationDto;
use Modules\Vendor\Models\VendorProfile;
use Modules\Vendor\Services\VendorService;

class VendorManager
{
    public function __construct(
        private readonly VendorService $vendorService,
    ) {
    }

    public function apply(VendorApplicationDto $dto): VendorProfile
    {
        return DB::transaction(function () use ($dto): VendorProfile {
            return $this->vendorService->apply($dto);
        });
    }

    public function updateProfile(int $userId, UpdateVendorProfileDto $dto): VendorProfile
    {
        return DB::transaction(function () use ($userId, $dto): VendorProfile {
            $profile = $this->vendorService->getByUserIdOrFail($userId);

            return $this->vendorService->updateProfile($profile, $dto);
        });
    }

    public function getMyProfile(int $userId): VendorProfile
    {
        return $this->vendorService->getByUserIdOrFail($userId);
    }
}
