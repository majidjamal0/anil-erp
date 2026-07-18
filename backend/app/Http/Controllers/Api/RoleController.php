<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return RoleResource::collection(Role::query()->with('permissions')->paginate());
    }

    public function store(): RoleResource
    {
        $data = request()->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('roles')->where('guard_name', 'web'),
            ],
            'permissions' => ['sometimes', 'array'],
            'permissions.*' => [
                'string',
                Rule::exists('permissions', 'name')->where('guard_name', 'web'),
            ],
        ]);

        $role = Role::create(['name' => $data['name'], 'guard_name' => 'web']);
        $role->syncPermissions($data['permissions'] ?? []);

        return new RoleResource($role->load('permissions'));
    }

    public function show(Role $role): RoleResource
    {
        return new RoleResource($role->load('permissions'));
    }

    public function update(Role $role): RoleResource
    {
        $this->ensureRoleIsMutable($role);

        $data = request()->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('roles')->where('guard_name', 'web')->ignore($role->id),
            ],
            'permissions' => ['sometimes', 'array'],
            'permissions.*' => [
                'string',
                Rule::exists('permissions', 'name')->where('guard_name', 'web'),
            ],
        ]);

        $role->update(['name' => $data['name']]);

        if (array_key_exists('permissions', $data)) {
            $role->syncPermissions($data['permissions']);
        }

        return new RoleResource($role->load('permissions'));
    }

    public function destroy(Role $role): JsonResponse
    {
        $this->ensureRoleIsMutable($role);
        $role->delete();

        return response()->json(status: 204);
    }

    private function ensureRoleIsMutable(Role $role): void
    {
        abort_if($role->name === 'Super Admin', 422, __('messages.protected_role'));
    }
}
