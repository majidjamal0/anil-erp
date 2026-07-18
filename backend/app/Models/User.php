<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasRoles;
    use HasUuids;
    use Notifiable;

    protected $fillable = ['name', 'email', 'password', 'locale', 'is_active'];

    protected $hidden = ['password', 'remember_token'];

    public function companies(): BelongsToMany { return $this->belongsToMany(Company::class)->withPivot(['access_level','is_default'])->withTimestamps(); }
    public function branches(): BelongsToMany { return $this->belongsToMany(Branch::class)->withPivot(['access_level','is_default'])->withTimestamps(); }
    public function warehouses(): BelongsToMany { return $this->belongsToMany(Warehouse::class, 'user_warehouse')->withPivot(['access_level','is_default'])->withTimestamps(); }
    public function hasGlobalOrganizationAccess(): bool { return $this->hasRole('Super Admin') || $this->can('organization.assign_access'); }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }
}
