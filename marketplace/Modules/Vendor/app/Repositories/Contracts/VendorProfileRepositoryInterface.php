<?php

declare(strict_types=1);

namespace Modules\Vendor\Repositories\Contracts;

use DateTimeInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Vendor\DTOs\UpdateVendorProfileDto;
use Modules\Vendor\DTOs\VendorApplicationDto;
use Modules\Vendor\Models\VendorProfile;

interface VendorProfileRepositoryInterface
{
    public function findById(int $id): ?VendorProfile;

    public function findByUserId(int $userId): ?VendorProfile;

    public function findBySlug(string $slug): ?VendorProfile;

    public function createFromApplication(VendorApplicationDto $dto, string $storeSlug): VendorProfile;

    public function update(VendorProfile $profile, UpdateVendorProfileDto $dto): VendorProfile;

    public function markApproved(VendorProfile $profile, DateTimeInterface $approvedAt): VendorProfile;

    public function markRejected(VendorProfile $profile, string $reason, DateTimeInterface $rejectedAt): VendorProfile;

    public function paginateByStatus(?string $status, int $perPage = 15): LengthAwarePaginator;
}
