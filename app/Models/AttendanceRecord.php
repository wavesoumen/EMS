<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AttendanceRecord extends Model
{
    protected $fillable = [
        'user_id', 'clock_in_requested_at', 'clock_in_approved_at',
        'clock_in_time', 'clock_out_time', 'approved_by',
        'status', 'total_minutes', 'date',
    ];

    protected $casts = [
        'clock_in_requested_at' => 'datetime',
        'clock_in_approved_at' => 'datetime',
        'clock_in_time' => 'datetime',
        'clock_out_time' => 'datetime',
        'date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getFormattedTotalTimeAttribute(): string
    {
        $hours = intdiv($this->total_minutes, 60);
        $mins = $this->total_minutes % 60;
        return sprintf('%dh %02dm', $hours, $mins);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'badge-warning',
            'approved' => 'badge-success',
            'rejected' => 'badge-danger',
            'clocked_out' => 'badge-info',
            default => 'badge-secondary',
        };
    }
}
