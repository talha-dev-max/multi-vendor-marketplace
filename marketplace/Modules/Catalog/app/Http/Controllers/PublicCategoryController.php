<?php

declare(strict_types=1);

namespace Modules\Catalog\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Modules\Catalog\Http\Resources\CategoryResource;
use Modules\Catalog\Managers\CategoryManager;
use Throwable;

class PublicCategoryController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly CategoryManager $categoryManager,
    ) {
    }

    public function index(): JsonResponse
    {
        try {
            return $this->success(
                CategoryResource::collection($this->categoryManager->listActive()),
            );
        } catch (Throwable $e) {
            Log::error('Public category list failed', ['exception' => $e]);

            return $this->error('Unable to fetch categories.', 500);
        }
    }
}
