<?php

declare(strict_types=1);

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Modules\Admin\Managers\AdminDashboardManager;
use Throwable;

class DashboardController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly AdminDashboardManager $dashboardManager,
    ) {
    }

    public function stats(): JsonResponse
    {
        try {
            return $this->success($this->dashboardManager->stats());
        } catch (Throwable $e) {
            Log::error('Admin dashboard stats failed', ['exception' => $e]);

            return $this->error('Unable to fetch stats.', 500);
        }
    }
}
