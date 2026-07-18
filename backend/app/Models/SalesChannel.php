<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesChannel extends Model
{
    use HasUuids, SoftDeletes;

    public const TYPES = ['physical_store', 'website', 'social', 'exhibition', 'wholesale', 'organizational', 'consignment', 'other'];

    protected $fillable = ['company_id', 'branch_id', 'name', 'code', 'type', 'is_active', 'requires_warehouse_selection', 'default_warehouse_id', 'settings', 'sort_order'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'requires_warehouse_selection' => 'boolean', 'settings' => 'array'];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function defaultWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'default_warehouse_id');
    }
}
