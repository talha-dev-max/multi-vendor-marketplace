<?php

declare(strict_types=1);

namespace Modules\Vendor\Services;

use Illuminate\Support\Str;
use Modules\Vendor\DTOs\UpdateVendorProfileDto;
use Modules\Vendor\DTOs\VendorApplicationDto;
use Modules\Vendor\Exceptions\VendorAlreadyAppliedException;
use Modules\Vendor\Exceptions\VendorProfileNotFoundException;
use Modules\Vendor\Models\VendorProfile;
use Modules\Vendor\Repositories\Contracts\VendorProfileRepositoryInterface;

class VendorService
{
    public function __construct(
        private readonly VendorProfileRepositoryInterface $profiles,
    ) {
    }

    public function apply(VendorApplicationDto $dto): VendorProfile
    {
        $existing = $this->profiles->findByUserId($dto->userId);

        if ($existing !== null) {
            throw new VendorAlreadyAppliedException();
        }

        return $this->profiles->createFromApplication($dto, $this->uniqueSlug($dto->storeName));
    }

    public function getByUserIdOrFail(int $userId): VendorProfile
    {
        $profile = $this->profiles->findByUserId($userId);

        if ($profile === null) {
            throw new VendorProfileNotFoundException();
        }

        return $profile;
    }

    public function updateProfile(VendorProfile $profile, UpdateVendorProfileDto $dto): VendorProfile
    {
        return $this->profiles->update($profile, $dto);
    }

    private function uniqueSlug(string $storeName): string
    {
        $base = Str::slug($storeName);
        $slug = $base;
        $i = 1;

        while ($this->profiles->findBySlug($slug) !== null) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }
}
