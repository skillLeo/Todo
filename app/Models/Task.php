<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'type',
        'total_days',
        'start_date',
        'due_date',
        'reminder_time',
        'priority',
        'status',
        'category',
    ];

    protected $casts = [
        'start_date' => 'date',
        'due_date' => 'date',
        'reminder_time' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function completions()
    {
        return $this->hasMany(TaskCompletion::class);
    }

    public function getProgressPercentageAttribute()
    {
        if ($this->type === 'daily' || !$this->total_days) {
            return 0;
        }

        $completedDays = $this->completions()->count();
        return min(100, round(($completedDays / $this->total_days) * 100, 1));
    }

    public function getCompletedDaysAttribute()
    {
        return $this->completions()->count();
    }

    public function getRemainingDaysAttribute()
    {
        if ($this->type !== 'specific_days' || !$this->total_days) {
            return null;
        }

        return max(0, $this->total_days - $this->completed_days);
    }

    public function isCompletedToday()
    {
        return $this->completions()
            ->whereDate('completion_date', Carbon::today())
            ->exists();
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }
}

class TaskCompletion extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'completion_date',
        'notes',
    ];

    protected $casts = [
        'completion_date' => 'date',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}