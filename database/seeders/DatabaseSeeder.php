<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Task;
use App\Models\TaskCompletion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create a demo user
        $user = User::create([
            'name' => 'Hassam Demo',
            'email' => 'demo@hassamtodo.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        echo "✅ Demo user created: demo@hassamtodo.com / password\n";

        // Create Daily Tasks
        $dailyTasks = [
            [
                'title' => 'Fajr Prayer',
                'description' => 'Complete morning Fajr prayer on time',
                'type' => 'daily',
                'category' => 'Worship',
                'priority' => 'high',
                'reminder_time' => '05:00',
            ],
            [
                'title' => 'Quran Reading',
                'description' => 'Read at least 2 pages of Quran',
                'type' => 'daily',
                'category' => 'Worship',
                'priority' => 'high',
                'reminder_time' => '06:00',
            ],
            [
                'title' => 'Morning Exercise',
                'description' => '30 minutes workout or jogging',
                'type' => 'daily',
                'category' => 'Health',
                'priority' => 'medium',
                'reminder_time' => '07:00',
            ],
            [
                'title' => 'Duhr Prayer',
                'description' => 'Afternoon prayer',
                'type' => 'daily',
                'category' => 'Worship',
                'priority' => 'high',
                'reminder_time' => '13:00',
            ],
            [
                'title' => 'Asr Prayer',
                'description' => 'Afternoon prayer',
                'type' => 'daily',
                'category' => 'Worship',
                'priority' => 'high',
                'reminder_time' => '16:30',
            ],
            [
                'title' => 'Maghrib Prayer',
                'description' => 'Evening prayer',
                'type' => 'daily',
                'category' => 'Worship',
                'priority' => 'high',
                'reminder_time' => '18:30',
            ],
            [
                'title' => 'Isha Prayer',
                'description' => 'Night prayer',
                'type' => 'daily',
                'category' => 'Worship',
                'priority' => 'high',
                'reminder_time' => '20:00',
            ],
            [
                'title' => 'Read 30 Minutes',
                'description' => 'Read educational or beneficial books',
                'type' => 'daily',
                'category' => 'Education',
                'priority' => 'medium',
                'reminder_time' => '21:00',
            ],
        ];

        foreach ($dailyTasks as $taskData) {
            $task = Task::create(array_merge($taskData, [
                'user_id' => $user->id,
                'status' => 'pending',
            ]));

            // Add some historical completions
            for ($i = 14; $i >= 0; $i--) {
                if (rand(1, 10) > 3) { // 70% completion rate
                    TaskCompletion::create([
                        'task_id' => $task->id,
                        'completion_date' => Carbon::today()->subDays($i),
                    ]);
                }
            }
        }

        echo "✅ Daily tasks created with historical data\n";

        // Create Specific Days Tasks (Challenges)
        $specificTasks = [
            [
                'title' => '40 Days Diet Challenge',
                'description' => 'Follow healthy diet plan for 40 consecutive days',
                'type' => 'specific_days',
                'total_days' => 40,
                'start_date' => Carbon::today()->subDays(15),
                'category' => 'Health',
                'priority' => 'high',
                'reminder_time' => '08:00',
            ],
            [
                'title' => '30 Days Programming Challenge',
                'description' => 'Code at least 1 hour every day',
                'type' => 'specific_days',
                'total_days' => 30,
                'start_date' => Carbon::today()->subDays(10),
                'category' => 'Education',
                'priority' => 'high',
                'reminder_time' => '14:00',
            ],
            [
                'title' => '21 Days Morning Routine',
                'description' => 'Wake up at 5 AM and follow morning routine',
                'type' => 'specific_days',
                'total_days' => 21,
                'start_date' => Carbon::today()->subDays(8),
                'category' => 'Personal',
                'priority' => 'medium',
                'reminder_time' => '05:00',
            ],
            [
                'title' => '60 Days Quran Completion',
                'description' => 'Complete reading entire Quran in 60 days',
                'type' => 'specific_days',
                'total_days' => 60,
                'start_date' => Carbon::today()->subDays(20),
                'category' => 'Worship',
                'priority' => 'high',
                'reminder_time' => '06:00',
            ],
            [
                'title' => '100 Days No Sugar Challenge',
                'description' => 'Avoid all processed sugar for 100 days',
                'type' => 'specific_days',
                'total_days' => 100,
                'start_date' => Carbon::today()->subDays(25),
                'category' => 'Health',
                'priority' => 'medium',
                'reminder_time' => '09:00',
            ],
        ];

        foreach ($specificTasks as $taskData) {
            $task = Task::create(array_merge($taskData, [
                'user_id' => $user->id,
                'status' => 'pending',
            ]));

            // Add completions based on start date
            $startDate = Carbon::parse($taskData['start_date']);
            $daysElapsed = $startDate->diffInDays(Carbon::today());
            
            for ($i = 0; $i <= min($daysElapsed, $taskData['total_days'] - 1); $i++) {
                if (rand(1, 10) > 2) { // 80% completion rate for challenges
                    TaskCompletion::create([
                        'task_id' => $task->id,
                        'completion_date' => $startDate->copy()->addDays($i),
                        'notes' => $i % 5 === 0 ? 'Feeling great! Making good progress.' : null,
                    ]);
                }
            }
        }

        echo "✅ Specific days tasks (challenges) created with progress data\n";

        // Create One-Time Tasks
        $oneTimeTasks = [
            [
                'title' => 'Complete Project Proposal',
                'description' => 'Finish and submit the project proposal document',
                'type' => 'one_time',
                'due_date' => Carbon::today()->addDays(3),
                'category' => 'Work',
                'priority' => 'high',
                'reminder_time' => '10:00',
            ],
            [
                'title' => 'Doctor Appointment',
                'description' => 'Annual health checkup appointment',
                'type' => 'one_time',
                'due_date' => Carbon::today()->addDays(5),
                'category' => 'Health',
                'priority' => 'high',
                'reminder_time' => '14:30',
            ],
            [
                'title' => 'Pay Electricity Bill',
                'description' => 'Pay monthly electricity bill before due date',
                'type' => 'one_time',
                'due_date' => Carbon::today()->addDays(7),
                'category' => 'Finance',
                'priority' => 'medium',
                'reminder_time' => '11:00',
            ],
            [
                'title' => 'Buy Groceries',
                'description' => 'Weekly grocery shopping',
                'type' => 'one_time',
                'due_date' => Carbon::today()->addDays(2),
                'category' => 'Personal',
                'priority' => 'medium',
                'reminder_time' => '17:00',
            ],
            [
                'title' => 'Call Family',
                'description' => 'Weekly call to check on family',
                'type' => 'one_time',
                'due_date' => Carbon::today()->addDays(1),
                'category' => 'Family',
                'priority' => 'high',
                'reminder_time' => '19:00',
            ],
            [
                'title' => 'Submit Tax Documents',
                'description' => 'Prepare and submit annual tax documents',
                'type' => 'one_time',
                'due_date' => Carbon::today()->addDays(14),
                'category' => 'Finance',
                'priority' => 'high',
                'reminder_time' => '10:00',
            ],
            [
                'title' => 'Renew Car Insurance',
                'description' => 'Renew car insurance before expiry',
                'type' => 'one_time',
                'due_date' => Carbon::today()->addDays(10),
                'category' => 'Finance',
                'priority' => 'medium',
                'reminder_time' => '12:00',
            ],
            [
                'title' => 'Organize Workspace',
                'description' => 'Clean and organize home office workspace',
                'type' => 'one_time',
                'due_date' => Carbon::today()->addDays(4),
                'category' => 'Personal',
                'priority' => 'low',
                'reminder_time' => '16:00',
            ],
        ];

        foreach ($oneTimeTasks as $taskData) {
            Task::create(array_merge($taskData, [
                'user_id' => $user->id,
                'status' => 'pending',
            ]));
        }

        echo "✅ One-time tasks created\n";

        // Create some completed tasks for statistics
        $completedTasks = [
            [
                'title' => 'Finish Online Course',
                'description' => 'Complete Laravel advanced course',
                'type' => 'specific_days',
                'total_days' => 30,
                'start_date' => Carbon::today()->subDays(45),
                'category' => 'Education',
                'priority' => 'high',
                'status' => 'completed',
            ],
            [
                'title' => '7 Day Water Challenge',
                'description' => 'Drink 8 glasses of water daily for 7 days',
                'type' => 'specific_days',
                'total_days' => 7,
                'start_date' => Carbon::today()->subDays(20),
                'category' => 'Health',
                'priority' => 'medium',
                'status' => 'completed',
            ],
        ];

        foreach ($completedTasks as $taskData) {
            $task = Task::create(array_merge($taskData, [
                'user_id' => $user->id,
            ]));

            // Add all completions
            $startDate = Carbon::parse($taskData['start_date']);
            for ($i = 0; $i < $taskData['total_days']; $i++) {
                TaskCompletion::create([
                    'task_id' => $task->id,
                    'completion_date' => $startDate->copy()->addDays($i),
                ]);
            }
        }

        echo "✅ Completed tasks created\n";

        // Create some archived tasks
        $archivedTasks = [
            [
                'title' => 'Old Project Task',
                'description' => 'Task from an old project',
                'type' => 'one_time',
                'category' => 'Work',
                'priority' => 'low',
                'status' => 'archived',
            ],
        ];

        foreach ($archivedTasks as $taskData) {
            Task::create(array_merge($taskData, [
                'user_id' => $user->id,
            ]));
        }

        echo "✅ Archived tasks created\n";

        // Summary
        echo "\n========================================\n";
        echo "✨ Database Seeded Successfully! ✨\n";
        echo "========================================\n";
        echo "Total Tasks Created: " . Task::count() . "\n";
        echo "Total Completions: " . TaskCompletion::count() . "\n";
        echo "\n📧 Login Credentials:\n";
        echo "Email: demo@hassamtodo.com\n";
        echo "Password: password\n";
        echo "========================================\n";
    }
}