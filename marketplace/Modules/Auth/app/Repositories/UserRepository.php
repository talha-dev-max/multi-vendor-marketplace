<?php

declare(strict_types=1);

namespace Modules\Auth\Repositories;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Auth\DTOs\RegisterDto;
use Modules\Auth\Repositories\Contracts\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    public function findById(int $id): ?User
    {
        return User::query()->find($id);
    }

    public function findByEmail(string $email): ?User
    {
        return User::query()->where('email', $email)->first();
    }

    public function createFromRegisterDto(RegisterDto $dto): User
    {
        return User::query()->create([
            'name' => $dto->name,
            'email' => $dto->email,
            'password' => $dto->password,
        ]);
    }

    public function updatePassword(User $user, string $plainPassword): User
    {
        $user->fill(['password' => $plainPassword])->save();

        return $user->fresh();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return User::query()->latest()->paginate($perPage);
    }
}
