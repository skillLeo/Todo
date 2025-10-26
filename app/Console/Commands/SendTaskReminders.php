<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Notifications\TaskReminderNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendTaskReminders extends Command
{
    protected $signature = 'tasks:send-reminders';
    protected $description = 'Send due/soon reminders for tasks & habits';

    public function handle(): int
    {
        // Iterate per user to apply user timezone
        Task::with('user')->chunkById(200, function($tasks) {
            $byUser = $tasks->groupBy('user_id');
            foreach ($byUser as $userId => $userTasks) {
                $user = $userTasks->first()->user;
                if (!$user) continue;

                $tz = $user->timezone ?? config('app.timezone', 'UTC');
                $now = Carbon::now($tz);
                $today = $now->copy()->startOfDay();

                foreach ($userTasks as $t) {
                    if (!$t->reminder_time) continue;

                    // Compute target reminder moment (today)
                    $reminderAt = Carbon::parse($t->reminder_time, $tz);
                    $reminderAt->setDateFrom($today);

                    if ($t->remind_before_minutes) {
                        $reminderAt->subMinutes($t->remind_before_minutes);
                    }

                    // Allow a window of +/- 30 seconds
                    if (abs($reminderAt->diffInSeconds($now)) <= 30) {
                        // Only remind if expected today
                        if ($t->isHabit()) {
                            if (!$t->expectedOn($today)) continue;
                        } else {
                            if (!$t->due_date || !$t->due_date->equalTo($today)) continue;
                        }

                        $user->notify(new TaskReminderNotification([
                            'title' => 'Task Reminder',
                            'body' => $t->title,
                            'task_id' => $t->id,
                            'due' => $today->toDateString(),
                        ]));
                    }
                }
            }
        });

        $this->info('Reminders processed.');
        return self::SUCCESS;
    }
}
