<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'employee_id',
        'phone',
        'department',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // Relationships
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function assets()
    {
        return $this->hasMany(Asset::class, 'responsible_user_id');
    }

    public function loans()
    {
        return $this->hasMany(Loan::class, 'user_id');
    }

    public function maintenanceRequests()
    {
        return $this->hasMany(Maintenance::class, 'requested_by');
    }

    public function disposalRequests()
    {
        return $this->hasMany(Disposal::class, 'requested_by');
    }

    // Helper methods
    public function hasRole($roleName)
    {
        return $this->role && $this->role->name === $roleName;
    }

    public function hasPermission($permissionName)
    {
        if (!$this->role) {
            return false;
        }
        
        return $this->role->permissions()
            ->where('name', $permissionName)
            ->exists();
    }

    public function hasAnyRole($roles)
    {
        return $this->role && in_array($this->role->name, (array) $roles);
    }
}
