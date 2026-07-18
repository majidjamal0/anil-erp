<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Company;
use App\Models\SalesChannel;
use App\Models\User;
use App\Models\Warehouse;
use App\Services\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class OrganizationAccessController extends Controller
{
    public function __construct(private readonly AuditLogger $audit) {}

    public function context(Request $request): JsonResponse
    {
        $user = $request->user();
        $global = $user->hasGlobalOrganizationAccess();
        $companies = $global
            ? Company::where('is_active', true)->get()
            : $user->companies()->where('is_active', true)->get();
        $companyIds = $companies->pluck('id');

        return response()->json([
            'accessible_companies' => $companies,
            'accessible_branches' => ($global ? Branch::query() : $user->branches())
                ->whereIn('company_id', $companyIds)
                ->where('is_active', true)
                ->get(),
            'accessible_warehouses' => ($global ? Warehouse::query() : $user->warehouses())
                ->whereIn('company_id', $companyIds)
                ->where('is_active', true)
                ->get(),
            'accessible_sales_channels' => SalesChannel::whereIn('company_id', $companyIds)
                ->where('is_active', true)
                ->get(),
            'defaults' => [
                'company_id' => $user->companies()->wherePivot('is_default', true)->value('companies.id'),
                'branch_id' => $user->branches()->wherePivot('is_default', true)->value('branches.id'),
                'warehouse_id' => $user->warehouses()->wherePivot('is_default', true)->value('warehouses.id'),
            ],
        ]);
    }

    public function show(User $user): JsonResponse
    {
        abort_unless(auth()->user()?->can('organization.assign_access') || auth()->user()?->hasRole('Super Admin'), 403);

        return response()->json([
            'companies' => $user->companies()->get(),
            'branches' => $user->branches()->get(),
            'warehouses' => $user->warehouses()->get(),
        ]);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        abort_unless($request->user()?->can('organization.assign_access') || $request->user()?->hasRole('Super Admin'), 403);

        $data = $request->validate([
            'companies' => 'array',
            'companies.*.id' => 'exists:companies,id',
            'companies.*.access_level' => Rule::in(['view', 'operate', 'approve', 'manage']),
            'companies.*.is_default' => 'boolean',
            'branches' => 'array',
            'branches.*.id' => 'exists:branches,id',
            'branches.*.access_level' => Rule::in(['view', 'operate', 'approve', 'manage']),
            'branches.*.is_default' => 'boolean',
            'warehouses' => 'array',
            'warehouses.*.id' => 'exists:warehouses,id',
            'warehouses.*.access_level' => Rule::in(['view', 'operate', 'approve', 'manage']),
            'warehouses.*.is_default' => 'boolean',
        ]);

        DB::transaction(function () use ($user, $data): void {
            foreach (['companies' => 'companies', 'branches' => 'branches', 'warehouses' => 'warehouses'] as $key => $relation) {
                $sync = [];

                foreach ($data[$key] ?? [] as $row) {
                    $sync[$row['id']] = [
                        'id' => (string) Str::uuid(),
                        'access_level' => $row['access_level'] ?? 'view',
                        'is_default' => $row['is_default'] ?? false,
                    ];
                }

                $user->{$relation}()->sync($sync);
            }

            $this->audit->log('organization.access.updated', $user, null, $data);
        });

        return $this->show($user);
    }
}
