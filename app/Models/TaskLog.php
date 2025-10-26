<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TaskLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id', 'user_id', 'date', 'completed', 'completed_at', 'note'
    ];

    protected $casts = [
        'date' => 'date',
        'completed_at' => 'datetime',
        'completed' => 'boolean',
    ];

    public function task() { return $this->belongsTo(Task::class); }
    public function user() { return $this->belongsTo(User::class); }
}
