<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OrganizationAccessController;
use App\Http\Controllers\Api\OrganizationResourceController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    DB::select('select 1');

    if (! app()->environment('testing')) {
        Redis::connection()->ping();
    }

    return response()->json([
        'status' => 'ok',
        'service' => 'anil-erp-api',
        'timestamp' => now()->toIso8601String(),
    ]);
})->name('health');

Route::prefix('auth')->group(function (): void {
    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:login');
    Route::post('forgot-password', [AuthController::class, 'forgot'])->middleware('throttle:6,1');
    Route::post('reset-password', [AuthController::class, 'reset'])->middleware('throttle:6,1');
});

Route::middleware(['auth:sanctum', 'active'])->group(function (): void {
    Route::get('auth/user', [AuthController::class, 'user']);
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::put('auth/password', [AuthController::class, 'changePassword']);

    Route::apiResource('users', UserController::class);
    foreach (['companies', 'branches', 'warehouse-types', 'warehouses', 'sales-channels'] as $resource) {
        Route::get($resource, [OrganizationResourceController::class, 'index'])->defaults('resource', $resource);
        Route::post($resource, [OrganizationResourceController::class, 'store'])->defaults('resource', $resource);
        Route::get($resource.'/{id}', [OrganizationResourceController::class, 'show'])->defaults('resource', $resource);
        Route::match(['put', 'patch'], $resource.'/{id}', [OrganizationResourceController::class, 'update'])->defaults('resource', $resource);
        Route::delete($resource.'/{id}', [OrganizationResourceController::class, 'destroy'])->defaults('resource', $resource);
    }
    Route::get('organization/context', [OrganizationAccessController::class, 'context']);
    Route::get('users/{user}/organization-access', [OrganizationAccessController::class, 'show']);
    Route::put('users/{user}/organization-access', [OrganizationAccessController::class, 'update']);
    Route::post('users/{user}/roles', [UserController::class, 'assignRole'])
        ->middleware('permission:users.update');
    Route::delete('users/{user}/roles/{role}', [UserController::class, 'removeRole'])
        ->middleware('permission:users.update');

    Route::middleware('can:manage-rbac')->group(function (): void {
        Route::apiResource('roles', RoleController::class);
        Route::apiResource('permissions', PermissionController::class);
    });
});
