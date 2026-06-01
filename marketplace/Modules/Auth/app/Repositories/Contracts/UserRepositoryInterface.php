<?php

declare(strict_types=1);

namespace Modules\Auth\Repositories\Contracts;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Auth\DTOs\RegisterDto;

interface UserRepositoryInterface
{
    public function findById(int $id): ?User;

    public function findByEmail(string $email): ?User;

    public function createFromRegisterDto(RegisterDto $dto): User;

    public function updatePassword(User $user, string $plainPassword): User;

    public function paginate(int $perPage = 15): LengthAwarePaginator;
}
