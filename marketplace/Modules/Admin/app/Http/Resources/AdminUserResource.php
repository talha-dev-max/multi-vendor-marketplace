<?php

declare(strict_types=1);

namespace Modules\Admin\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminUserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at?->toIso8601String(),
            'roles' => $this->getRoleNames()->all(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
