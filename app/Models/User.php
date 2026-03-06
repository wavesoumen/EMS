<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $fillable = [
        'name', 'mobile', 'role_id', 'company_id', 'supervisor_id', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function subordinates()
    {
        return $this->hasMany(User::class, 'supervisor_id');
    }

    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    public function timeBank()
    {
        return $this->hasOne(TimeBank::class);
    }

    public function isAdmin(): bool
    {
        return $this->role && $this->role->level === 1;
    }

    public function isSupervisorOf(User $user): bool
    {
        return $user->supervisor_id === $this->id;
    }

    public function hasPermission(string $permission): bool
    {
        return $this->role && $this->role->hasPermission($permission);
    }

    public function getTotalTimeAttribute(): int
    {
        return $this->timeBank ? $this->timeBank->total_minutes : 0;
    }

    public function getFormattedTotalTimeAttribute(): string
    {
        $minutes = $this->total_time;
        $hours = intdiv($minutes, 60);
        $mins = $minutes % 60;
        return sprintf('%dh %02dm', $hours, $mins);
    }
}
