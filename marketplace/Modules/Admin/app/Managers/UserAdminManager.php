<?php

declare(strict_types=1);

namespace Modules\Admin\Managers;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Admin\Services\UserAdminService;

class UserAdminManager
{
    public function __construct(
        private readonly UserAdminService $userAdminService,
    ) {
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->userAdminService->paginate($perPage);
    }
}
