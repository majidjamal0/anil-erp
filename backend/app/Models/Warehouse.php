<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warehouse extends Model
{
    use HasUuids, SoftDeletes;
    protected $fillable=['company_id','branch_id','warehouse_type_id','manager_user_id','name','code','address','phone','is_active','is_sellable','is_shippable','allocation_priority','metadata'];
    protected function casts(): array { return ['is_active'=>'boolean','is_sellable'=>'boolean','is_shippable'=>'boolean','metadata'=>'array']; }
    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
    public function branch(): BelongsTo { return $this->belongsTo(Branch::class); }
    public function type(): BelongsTo { return $this->belongsTo(WarehouseType::class, 'warehouse_type_id'); }
    public function users(): BelongsToMany { return $this->belongsToMany(User::class, 'user_warehouse')->withPivot(['access_level','is_default'])->withTimestamps(); }
    public function scopeOperational(Builder $q): Builder { return $q->where('is_active', true)->whereDoesntHave('branch', fn ($b) => $b->where('is_external', true)); }
}
