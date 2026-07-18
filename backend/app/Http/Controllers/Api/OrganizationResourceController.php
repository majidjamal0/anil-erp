<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Company;
use App\Models\SalesChannel;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WarehouseType;
use App\Services\AuditLogger;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class OrganizationResourceController extends Controller
{
    public function __construct(private readonly AuditLogger $audit) {}

    public function index(Request $request): JsonResponse
    {
        $resource = $request->route('resource');
        $this->authorizePermission($resource, 'view');
        $query = $this->model($resource)::query()->with($this->with($resource));

        foreach (['company_id', 'branch_id', 'type', 'is_active'] as $filter) {
            if ($request->filled($filter)) {
                $query->where($filter, $request->boolean($filter, null) ?? $request->input($filter));
            }
        }

        if ($request->filled('q')) {
            $query->where(fn ($inner) => $inner
                ->where('name', 'like', '%'.$request->q.'%')
                ->orWhere('code', 'like', '%'.$request->q.'%'));
        }

        $this->scopeVisible($query, $request->user(), $resource);

        return response()->json($query
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(min((int) $request->input('per_page', 15), 100)));
    }

    public function store(Request $request): JsonResponse
    {
        $resource = $request->route('resource');
        $this->authorizePermission($resource, 'create');
        $data = $this->validateData($request, $resource);

        $model = DB::transaction(fn () => tap(
            $this->model($resource)::create($data),
            fn ($created) => $this->audit->log($resource.'.created', $created, null, $created->toArray())
        ));

        return response()->json($model->load($this->with($resource)), 201);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $resource = $request->route('resource');
        $this->authorizePermission($resource, 'view');
        $model = $this->model($resource)::with($this->with($resource))->findOrFail($id);
        abort_unless($this->canSee($request->user(), $model), 403);

        return response()->json($model);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $resource = $request->route('resource');
        $this->authorizePermission($resource, 'update');
        $model = $this->model($resource)::findOrFail($id);
        abort_unless($this->canSee($request->user(), $model), 403);
        $data = $this->validateData($request, $resource, $model);
        $old = $model->toArray();

        if (isset($data['company_id']) && isset($model->company_id) && $data['company_id'] !== $model->company_id) {
            abort(422, 'Company cannot be changed.');
        }

        DB::transaction(function () use ($model, $data, $old, $resource): void {
            $model->update($data);
            $this->audit->log($resource.'.updated', $model, $old, $model->fresh()->toArray());
        });

        return response()->json($model->fresh()->load($this->with($resource)));
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        $resource = $request->route('resource');
        $this->authorizePermission($resource, 'delete');
        $model = $this->model($resource)::findOrFail($id);
        abort_unless($this->canSee($request->user(), $model), 403);

        if ($model instanceof Branch && $model->warehouses()->where('is_active', true)->exists()) {
            return response()->json(['message' => 'Active warehouses block branch deletion.'], 409);
        }

        DB::transaction(function () use ($model, $resource): void {
            $old = $model->toArray();
            $model->delete();
            $this->audit->log($resource.'.deleted', $model, $old, null);
        });

        return response()->json(null, 204);
    }

    private function model(string $resource): string
    {
        return [
            'companies' => Company::class,
            'branches' => Branch::class,
            'warehouse-types' => WarehouseType::class,
            'warehouses' => Warehouse::class,
            'sales-channels' => SalesChannel::class,
        ][$resource] ?? abort(404);
    }

    private function with(string $resource): array
    {
        return match ($resource) {
            'branches' => ['company', 'parent'],
            'warehouses' => ['company', 'branch', 'type'],
            'warehouse-types' => ['company'],
            'sales-channels' => ['company', 'branch', 'defaultWarehouse'],
            default => [],
        };
    }

    private function permBase(string $resource): string
    {
        return str_replace('-', '_', $resource);
    }

    private function authorizePermission(string $resource, string $action): void
    {
        $permission = $this->permBase($resource).($resource === 'warehouse-types' ? '.manage' : '.'.$action);
        abort_unless(auth()->user()?->can($permission) || auth()->user()?->hasRole('Super Admin'), 403);
    }

    private function scopeVisible($query, User $user, string $resource): void
    {
        if ($user->hasGlobalOrganizationAccess()) {
            return;
        }

        if ($resource === 'companies') {
            $query->whereIn('id', $user->companies()->pluck('companies.id'));
        } elseif ($resource === 'branches') {
            $query->whereIn('id', $user->branches()->pluck('branches.id'));
        } elseif ($resource === 'warehouses') {
            $query->whereIn('id', $user->warehouses()->pluck('warehouses.id'));
        } else {
            $query->whereIn('company_id', $user->companies()->pluck('companies.id'));
        }
    }

    private function canSee(User $user, Model $model): bool
    {
        if ($user->hasGlobalOrganizationAccess()) {
            return true;
        }

        if ($model instanceof Company) {
            return $user->companies()->whereKey($model->id)->exists();
        }

        if ($model instanceof Branch) {
            return $user->branches()->whereKey($model->id)->exists()
                || $user->companies()->whereKey($model->company_id)->exists();
        }

        if ($model instanceof Warehouse) {
            return $user->warehouses()->whereKey($model->id)->exists()
                || $user->companies()->whereKey($model->company_id)->exists();
        }

        return isset($model->company_id) && $user->companies()->whereKey($model->company_id)->exists();
    }

    private function validateData(Request $request, string $resource, ?Model $model = null): array
    {
        $rules = [
            'companies' => ['name' => 'required|string', 'code' => 'required|string', 'email' => 'nullable|email', 'is_active' => 'boolean', 'settings' => 'nullable|array'],
            'branches' => ['company_id' => 'required|exists:companies,id', 'name' => 'required|string', 'code' => 'required|string', 'type' => ['required', Rule::in(Branch::TYPES)], 'parent_id' => 'nullable|exists:branches,id', 'manager_user_id' => 'nullable|exists:users,id', 'is_active' => 'boolean', 'is_operational' => 'boolean', 'is_external' => 'boolean', 'metadata' => 'nullable|array'],
            'warehouse-types' => ['company_id' => 'required|exists:companies,id', 'name' => 'required|string', 'code' => 'required|string', 'is_active' => 'boolean'],
            'warehouses' => ['company_id' => 'required|exists:companies,id', 'branch_id' => 'nullable|exists:branches,id', 'warehouse_type_id' => 'required|exists:warehouse_types,id', 'name' => 'required|string', 'code' => 'required|string', 'is_active' => 'boolean', 'is_sellable' => 'boolean', 'is_shippable' => 'boolean', 'metadata' => 'nullable|array'],
            'sales-channels' => ['company_id' => 'required|exists:companies,id', 'branch_id' => 'nullable|exists:branches,id', 'name' => 'required|string', 'code' => 'required|string', 'type' => ['required', Rule::in(SalesChannel::TYPES)], 'requires_warehouse_selection' => 'boolean', 'default_warehouse_id' => 'nullable|exists:warehouses,id', 'settings' => 'nullable|array'],
        ];

        $data = $request->validate($rules[$resource]);

        if (in_array($data['type'] ?? '', ['website', 'social']) && ! ($data['requires_warehouse_selection'] ?? false)) {
            abort(422, 'Online/social channels require warehouse selection.');
        }

        return $data + Arr::only($request->all(), [
            'legal_name', 'national_id', 'economic_code', 'phone', 'address', 'city', 'province', 'postal_code',
            'description', 'sort_order', 'allocation_priority', 'default_locale', 'default_currency', 'timezone',
        ]);
    }
}
