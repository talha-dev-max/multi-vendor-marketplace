<?php

declare(strict_types=1);

namespace Modules\Vendor\Http\Controllers;

use App\Exceptions\Domain\DomainException;
use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Vendor\Http\Requests\ApplyVendorRequest;
use Modules\Vendor\Http\Requests\UpdateVendorProfileRequest;
use Modules\Vendor\Http\Resources\VendorProfileResource;
use Modules\Vendor\Managers\VendorManager;
use Throwable;

class VendorApplicationController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly VendorManager $vendorManager,
    ) {
    }

    public function apply(ApplyVendorRequest $request): JsonResponse
    {
        try {
            $profile = $this->vendorManager->apply($request->toDto());

            return $this->success(
                VendorProfileResource::make($profile),
                'Vendor application submitted.',
                201,
            );
        } catch (DomainException $e) {
            return $this->error($e->getMessage(), $e->httpStatus());
        } catch (Throwable $e) {
            Log::error('Vendor application failed', ['exception' => $e]);

            return $this->error('Unable to submit application.', 500);
        }
    }

    public function myProfile(Request $request): JsonResponse
    {
        try {
            $profile = $this->vendorManager->getMyProfile((int) $request->user()->id);

            return $this->success(VendorProfileResource::make($profile));
        } catch (DomainException $e) {
            return $this->error($e->getMessage(), $e->httpStatus());
        } catch (Throwable $e) {
            Log::error('Fetch vendor profile failed', ['exception' => $e]);

            return $this->error('Unable to fetch profile.', 500);
        }
    }

    public function updateMyProfile(UpdateVendorProfileRequest $request): JsonResponse
    {
        try {
            $profile = $this->vendorManager->updateProfile((int) $request->user()->id, $request->toDto());

            return $this->success(VendorProfileResource::make($profile), 'Profile updated.');
        } catch (DomainException $e) {
            return $this->error($e->getMessage(), $e->httpStatus());
        } catch (Throwable $e) {
            Log::error('Update vendor profile failed', ['exception' => $e]);

            return $this->error('Unable to update profile.', 500);
        }
    }
}
