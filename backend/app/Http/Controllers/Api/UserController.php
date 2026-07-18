<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(User::class, 'user');
    }

    public function index(): AnonymousResourceCollection
    {
        return UserResource::collection(
            User::query()->with('roles')->latest()->paginate()
        );
    }

    public function store(UserRequest $request): UserResource
    {
        $user = User::create($request->safe()->except('roles'));
        $user->syncRoles($request->input('roles', []));
        $this->audit('user.created', $user, [], $this->auditValues($user));

        return new UserResource($user->load('roles'));
    }

    public function show(User $user): UserResource
    {
        return new UserResource($user->load('roles'));
    }

    public function update(UserRequest $request, User $user): UserResource
    {
        $oldValues = $this->auditValues($user);
        $attributes = $request->safe()->except(['roles', 'password']);

        if ($request->filled('password')) {
            $attributes['password'] = $request->string('password')->toString();
        }

        $user->update($attributes);

        if ($request->has('roles')) {
            $roles = $request->input('roles', []);

            abort_if(
                $user->hasRole('Super Admin') && ! in_array('Super Admin', $roles, true),
                422,
                __('messages.protected_role')
            );

            $user->syncRoles($roles);
        }

        $this->audit('user.updated', $user, $oldValues, $this->auditValues($user->fresh()));

        return new UserResource($user->load('roles'));
    }

    public function destroy(User $user): JsonResponse
    {
        abort_if(auth()->id() === $user->id, 422, __('messages.cannot_delete_self'));
        abort_if($user->hasRole('Super Admin'), 422, __('messages.protected_role'));

        $oldValues = $this->auditValues($user);
        $user->delete();
        $this->audit('user.deleted', $user, $oldValues, []);

        return response()->json(status: 204);
    }

    public function assignRole(User $user): UserResource
    {
        $data = request()->validate([
            'role' => [
                'required',
                'string',
                Rule::exists('roles', 'name')->where('guard_name', 'web'),
            ],
        ]);

        $user->assignRole($data['role']);
        $this->audit('user.role_assigned', $user, [], ['role' => $data['role']]);

        return new UserResource($user->load('roles'));
    }

    public function removeRole(User $user, string $role): UserResource
    {
        abort_if($role === 'Super Admin', 422, __('messages.protected_role'));

        $user->removeRole($role);
        $this->audit('user.role_removed', $user, ['role' => $role], []);

        return new UserResource($user->load('roles'));
    }

    private function audit(string $event, User $user, array $oldValues, array $newValues): void
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'event' => $event,
            'auditable_type' => User::class,
            'auditable_id' => $user->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    private function auditValues(User $user): array
    {
        return $user->only(['id', 'name', 'email', 'locale', 'is_active']);
    }
}
