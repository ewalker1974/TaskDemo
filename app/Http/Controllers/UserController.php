<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Task;

class UserController
{
    private const DEFAULT_PAGE_SIZE = 10;

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $page = $request->integer('page', 1);
        $size = $request->integer('size', self::DEFAULT_PAGE_SIZE);

        if (!$page) {
            $page = 1;
        }
        if (!$size) {
            $size = self::DEFAULT_PAGE_SIZE;
        }

        $pageData = User::query()
            ->orderBy('id')
            ->paginate($size, ['*'], 'page', $page);

        return new JsonResponse([
            'page' => $page,
            'last_page' => $pageData->lastPage(),
            'per_page' => $size,
            'total' => $pageData->total(),
            'data' => $pageData->items(),
        ]);
    }

    /**
     * @param User $user
     * @param Request $request
     * @return JsonResponse
     */
    public function userTasks(User $user, Request $request): JsonResponse
    {
        $page = $request->integer('page', 1);
        $size = $request->integer('size', self::DEFAULT_PAGE_SIZE);

        if (!$page) {
            $page = 1;
        }
        if (!$size) {
            $size = self::DEFAULT_PAGE_SIZE;
        }

        $page = $request->get('page', 1);
        $pageData = Task::query()
            ->where('assigned_user_id', $user->id)
            ->orderBy('id')
            ->paginate($size, ['*'], 'page', $page);

        return new JsonResponse([
            'page' => $page,
            'last_page' => $pageData->lastPage(),
            'per_page' => $size,
            'total' => $pageData->total(),
            'data' => $pageData->items(),
        ]);
    }
}
