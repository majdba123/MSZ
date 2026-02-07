<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Http\Resources\Auth\UserResource;
use App\Models\User;
use App\Services\Admin\UserService;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function __construct(public UserService $userService) {}

    /**
     * List all users.
     */
    public function index(): JsonResponse
    {
        $users = User::query()
            ->latest()
            ->paginate(15);

        return response()->json([
            'message' => __('Users retrieved successfully.'),
            'data' => UserResource::collection($users),
            'meta' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ],
        ]);
    }

    /**
     * Show a single user.
     */
    public function show(User $user): JsonResponse
    {
        return response()->json([
            'message' => __('User retrieved successfully.'),
            'data' => new UserResource($user),
        ]);
    }

    /**
     * Create a new user.
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = $this->userService->create($request->validated());

        return response()->json([
            'message' => __('User created successfully.'),
            'data' => new UserResource($user),
        ], 201);
    }

    /**
     * Update an existing user.
     */
    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $user = $this->userService->update($user, $request->validated());

        return response()->json([
            'message' => __('User updated successfully.'),
            'data' => new UserResource($user),
        ]);
    }

    /**
     * Delete a user.
     */
    public function destroy(User $user): JsonResponse
    {
        $this->userService->delete($user);

        return response()->json([
            'message' => __('User deleted successfully.'),
        ]);
    }
}
