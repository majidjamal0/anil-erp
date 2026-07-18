<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use HasUuids, SoftDeletes;

    public const TYPES = ['retail_branch','workshop','office','independent','other'];
    protected $fillable = ['company_id','name','code','type','parent_id','manager_user_id','phone','email','address','city','province','postal_code','is_active','is_operational','is_external','sort_order','metadata'];
    protected function casts(): array { return ['is_active'=>'boolean','is_operational'=>'boolean','is_external'=>'boolean','metadata'=>'array']; }
    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
    public function parent(): BelongsTo { return $this->belongsTo(self::class, 'parent_id'); }
    public function warehouses(): HasMany { return $this->hasMany(Warehouse::class); }
    public function users(): BelongsToMany { return $this->belongsToMany(User::class)->withPivot(['access_level','is_default'])->withTimestamps(); }
    public function scopeForCompany(Builder $query, string $companyId): Builder { return $query->where('company_id', $companyId); }
}
