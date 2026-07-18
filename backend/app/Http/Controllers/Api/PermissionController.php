<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PermissionResource;
use App\Models\Permission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;

class PermissionController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return PermissionResource::collection(
            Permission::query()->orderBy('name')->paginate(100)
        );
    }

    public function store(): PermissionResource
    {
        $data = request()->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('permissions')->where('guard_name', 'web'),
            ],
        ]);

        return new PermissionResource(
            Permission::create(['name' => $data['name'], 'guard_name' => 'web'])
        );
    }

    public function show(Permission $permission): PermissionResource
    {
        return new PermissionResource($permission);
    }

    public function update(Permission $permission): PermissionResource
    {
        $data = request()->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('permissions')
                    ->where('guard_name', 'web')
                    ->ignore($permission->id),
            ],
        ]);

        $permission->update($data);

        return new PermissionResource($permission);
    }

    public function destroy(Permission $permission): JsonResponse
    {
        $permission->delete();

        return response()->json(status: 204);
    }
}
