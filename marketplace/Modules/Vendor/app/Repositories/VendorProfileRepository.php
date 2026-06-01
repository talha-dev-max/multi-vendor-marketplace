<?php

declare(strict_types=1);

namespace Modules\Vendor\Repositories;

use DateTimeInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Vendor\DTOs\UpdateVendorProfileDto;
use Modules\Vendor\DTOs\VendorApplicationDto;
use Modules\Vendor\Models\VendorProfile;
use Modules\Vendor\Repositories\Contracts\VendorProfileRepositoryInterface;

class VendorProfileRepository implements VendorProfileRepositoryInterface
{
    public function findById(int $id): ?VendorProfile
    {
        return VendorProfile::query()->with('user')->find($id);
    }

    public function findByUserId(int $userId): ?VendorProfile
    {
        return VendorProfile::query()->where('user_id', $userId)->first();
    }

    public function findBySlug(string $slug): ?VendorProfile
    {
        return VendorProfile::query()->where('store_slug', $slug)->first();
    }

    public function createFromApplication(VendorApplicationDto $dto, string $storeSlug): VendorProfile
    {
        return VendorProfile::query()->create([
            'user_id' => $dto->userId,
            'store_name' => $dto->storeName,
            'store_slug' => $storeSlug,
            'description' => $dto->description,
            'status' => VendorProfile::STATUS_PENDING,
        ]);
    }

    public function update(VendorProfile $profile, UpdateVendorProfileDto $dto): VendorProfile
    {
        $profile->fill([
            'store_name' => $dto->storeName,
            'description' => $dto->description,
        ])->save();

        return $profile->fresh();
    }

    public function markApproved(VendorProfile $profile, DateTimeInterface $approvedAt): VendorProfile
    {
        $profile->fill([
            'status' => VendorProfile::STATUS_APPROVED,
            'approved_at' => $approvedAt,
            'rejected_at' => null,
            'rejection_reason' => null,
        ])->save();

        return $profile->fresh();
    }

    public function markRejected(VendorProfile $profile, string $reason, DateTimeInterface $rejectedAt): VendorProfile
    {
        $profile->fill([
            'status' => VendorProfile::STATUS_REJECTED,
            'rejected_at' => $rejectedAt,
            'rejection_reason' => $reason,
            'approved_at' => null,
        ])->save();

        return $profile->fresh();
    }

    public function paginateByStatus(?string $status, int $perPage = 15): LengthAwarePaginator
    {
        $query = VendorProfile::query()->with('user')->latest();

        if ($status !== null) {
            $query->where('status', $status);
        }

        return $query->paginate($perPage);
    }
}
