<?php

use App\Models\Branch;
use App\Models\Warehouse;
use Illuminate\Support\Str;

it('relates UUID warehouses to their branch', function (): void {
    $branch = Branch::create(['code' => 'TEH-01', 'name' => 'Tehran']);
    $warehouse = $branch->warehouses()->create(['code' => 'TEH-W01', 'name' => 'Main']);

    expect(Str::isUuid($branch->id))->toBeTrue()
        ->and(Str::isUuid($warehouse->id))->toBeTrue()
        ->and($warehouse->branch->is($branch))->toBeTrue();
});

it('prevents deleting a branch that has a warehouse', function (): void {
    $branch = Branch::create(['code' => 'TEH-02', 'name' => 'Tehran 2']);
    Warehouse::create(['branch_id' => $branch->id, 'code' => 'TEH-W02', 'name' => 'Reserve']);

    expect(fn () => $branch->forceDelete())->toThrow(Throwable::class);
});
