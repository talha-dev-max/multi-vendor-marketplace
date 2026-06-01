<?php

declare(strict_types=1);

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Admin\Http\Resources\AdminUserResource;
use Modules\Admin\Managers\UserAdminManager;
use Throwable;

class UserController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly UserAdminManager $userAdminManager,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $paginator = $this->userAdminManager->paginate(
                (int) $request->query('per_page', 15),
            );

            return $this->success([
                'items' => AdminUserResource::collection($paginator->items()),
                'meta' => [
                    'current_page' => $paginator->currentPage(),
                    'last_page' => $paginator->lastPage(),
                    'per_page' => $paginator->perPage(),
                    'total' => $paginator->total(),
                ],
            ]);
        } catch (Throwable $e) {
            Log::error('Admin list users failed', ['exception' => $e]);

            return $this->error('Unable to fetch users.', 500);
        }
    }
}
