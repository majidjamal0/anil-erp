<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class WarehouseType extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = ['company_id', 'name', 'code', 'description', 'is_sellable', 'is_shippable', 'supports_raw_materials', 'supports_work_in_progress', 'supports_finished_goods', 'is_active', 'sort_order'];

    protected function casts(): array
    {
        return [
            'is_sellable' => 'boolean',
            'is_shippable' => 'boolean',
            'supports_raw_materials' => 'boolean',
            'supports_work_in_progress' => 'boolean',
            'supports_finished_goods' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
