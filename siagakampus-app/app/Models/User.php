<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Reports submitted by this user.
     */
    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'user_id');
    }

    /**
     * Audit logs created by this user.
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class, 'user_id');
    }

    /**
     * Field assignments assigned to this user.
     */
    public function fieldAssignments(): HasMany
    {
        return $this->hasMany(FieldAssignment::class, 'user_id');
    }

    /**
     * Role checking helper.
     */
    public function hasRole(string|array $roles): bool
    {
        if (is_string($roles)) {
            $roles = explode('|', $roles);
        }

        $normalized = array_map(fn ($r) => strtolower(trim($r)), $roles);
        $userRole = strtolower($this->role ?? '');

        return in_array($userRole, $normalized, true);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(['admin', 'superadmin']);
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('superadmin');
    }
}
