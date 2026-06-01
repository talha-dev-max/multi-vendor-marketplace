<?php

declare(strict_types=1);

namespace Modules\Admin\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Auth\Repositories\Contracts\UserRepositoryInterface;

class UserAdminService
{
    public function __construct(
        private readonly UserRepositoryInterface $users,
    ) {
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->users->paginate($perPage);
    }
}
