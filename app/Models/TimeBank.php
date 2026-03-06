<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimeBank extends Model
{
    protected $fillable = ['user_id', 'total_minutes'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFormattedTimeAttribute(): string
    {
        $hours = intdiv($this->total_minutes, 60);
        $mins = $this->total_minutes % 60;
        return sprintf('%dh %02dm', $hours, $mins);
    }

    public function addMinutes(int $minutes): void
    {
        $this->total_minutes += $minutes;
        $this->save();
    }
}
