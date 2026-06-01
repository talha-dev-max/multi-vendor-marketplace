<?php

declare(strict_types=1);

namespace Modules\Vendor\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Vendor\Exceptions\VendorProfileNotFoundException;
use Modules\Vendor\Models\VendorProfile;
use Modules\Vendor\Repositories\Contracts\VendorProfileRepositoryInterface;

class VendorApprovalService
{
    public function __construct(
        private readonly VendorProfileRepositoryInterface $profiles,
    ) {
    }

    public function listByStatus(?string $status, int $perPage = 15): LengthAwarePaginator
    {
        return $this->profiles->paginateByStatus($status, $perPage);
    }

    public function findByIdOrFail(int $id): VendorProfile
    {
        $profile = $this->profiles->findById($id);

        if ($profile === null) {
            throw new VendorProfileNotFoundException();
        }

        return $profile;
    }

    public function approve(VendorProfile $profile): VendorProfile
    {
        return $this->profiles->markApproved($profile, now());
    }

    public function reject(VendorProfile $profile, string $reason): VendorProfile
    {
        return $this->profiles->markRejected($profile, $reason, now());
    }
}
