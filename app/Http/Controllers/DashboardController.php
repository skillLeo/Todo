<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskCompletion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class DashboardController extends Controller
{
    use AuthorizesRequests;

    /**
     * Show the main dashboard with all tasks.
     */
    public function index()
    {
        $user = Auth::user();

        // Tasks grouped by type for dashboard
        $dailyTasks = $this->getDailyTasks($user);
        $specificDaysTasks = $this->getSpecificDaysTasks($user);
        $oneTimeTasks = $this->getOneTimeTasks($user);

        // Dashboard "stats cards"
        $stats = $this->getDashboardStats($user);

        // (optional extra data sections - not currently rendered in blade but keeping for future upgrades)
        $upcomingTasks = $this->getUpcomingTasks($user);
        $recentActivity = $this->getRecentActivity($user);

        return view('tasks.index', compact(
            'dailyTasks',
            'specificDaysTasks',
            'oneTimeTasks',
            'stats',
            'upcomingTasks',
            'recentActivity'
        ));
    }
    private function getDashboardStats($user)
    {
        $today = Carbon::today();

        // how many active tasks (status pending)
        $totalTasks = Task::where('user_id', $user->id)
            ->where('status', 'pending')
            ->count();

        // how many completions today
        $completedToday = TaskCompletion::whereHas('task', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->whereDate('completion_date', $today)
            ->count();

        // all-time completions
        $totalCompleted = TaskCompletion::whereHas('task', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->count();

        return [
            'total_tasks'      => $totalTasks,
            'completed_today'  => $completedToday,
            'total_completed'  => $totalCompleted,
        ];
    }


    /**
     * Show detailed statistics page.
     */
    public function statistics()
    {
        $user = Auth::user();

        // Overall statistics
        $overallStats = $this->getOverallStatistics($user);

        // Category breakdown
        $categoryStats = $this->getCategoryStatistics($user);

        // Weekly performance
        $weeklyPerformance = $this->getWeeklyPerformance($user);

        // Monthly trends
        $monthlyTrends = $this->getMonthlyTrends($user);

        // Achievement milestones
        $achievements = $this->getAchievements($user);

        return view('tasks.statistics', compact(
            'overallStats',
            'categoryStats',
            'weeklyPerformance',
            'monthlyTrends',
            'achievements'
        ));
    }

    /**
     * Get daily tasks with completion status.
     */
    private function getDailyTasks($user)
    {
        return Task::where('user_id', $user->id)
            ->where('type', 'daily')
            ->where('status', 'pending')
            ->with(['completions' => function($query) {
                $query->whereDate('completion_date', Carbon::today());
            }])
            ->orderByRaw("FIELD(priority, 'high', 'medium', 'low')")
            ->orderBy('reminder_time', 'asc')
            ->get();
    }

    /**
     * Get specific days tasks with progress.
     */
    private function getSpecificDaysTasks($user)
    {
        return Task::where('user_id', $user->id)
            ->where('type', 'specific_days')
            ->where('status', 'pending')
            ->withCount('completions')
            ->orderByRaw("FIELD(priority, 'high', 'medium', 'low')")
            ->orderBy('start_date', 'asc')
            ->get();
    }

    /**
     * Get one-time tasks sorted by priority and due date.
     */
    private function getOneTimeTasks($user)
    {
        return Task::where('user_id', $user->id)
            ->where('type', 'one_time')
            ->where('status', 'pending')
            ->orderBy('due_date', 'asc')
            ->orderByRaw("FIELD(priority, 'high', 'medium', 'low')")
            ->get();
    }

    /**
     * Get comprehensive statistics.
     */
    private function getComprehensiveStats($user)
    {
        $today = Carbon::today();
        $weekStart = Carbon::now()->startOfWeek();
        $monthStart = Carbon::now()->startOfMonth();
        $yearStart = Carbon::now()->startOfYear();

        return [
            'total_active_tasks' => Task::where('user_id', $user->id)
                ->where('status', 'pending')
                ->count(),
            
            'completed_today' => TaskCompletion::whereHas('task', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->whereDate('completion_date', $today)
                ->count(),
            
            'total_completions' => TaskCompletion::whereHas('task', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->count(),
            
            'completed_this_week' => TaskCompletion::whereHas('task', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->whereBetween('completion_date', [$weekStart, $today])
                ->count(),
            
            'completed_this_month' => TaskCompletion::whereHas('task', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->whereBetween('completion_date', [$monthStart, $today])
                ->count(),
            
            'completed_this_year' => TaskCompletion::whereHas('task', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->whereBetween('completion_date', [$yearStart, $today])
                ->count(),
            
            'current_streak' => $this->calculateStreak($user),
            
            'longest_streak' => $this->calculateLongestStreak($user),
            
            'completion_rate' => $this->calculateCompletionRate($user),
            
            'total_completed_tasks' => Task::where('user_id', $user->id)
                ->where('status', 'completed')
                ->count(),
        ];
    }

    /**
     * Calculate current streak (consecutive days with completions).
     */
    private function calculateStreak($user)
    {
        $streak = 0;
        $date = Carbon::today();

        while (true) {
            $hasCompletion = TaskCompletion::whereHas('task', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->whereDate('completion_date', $date)->exists();

            if ($hasCompletion) {
                $streak++;
                $date->subDay();
            } else {
                // Allow one day gap (yesterday might be off day)
                if ($streak === 0 && !$date->isToday()) {
                    $date->subDay();
                    continue;
                }
                break;
            }

            // Prevent infinite loop
            if ($streak > 365) break;
        }

        return $streak;
    }

    /**
     * Calculate longest streak ever.
     */
    private function calculateLongestStreak($user)
    {
        $completions = TaskCompletion::whereHas('task', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })
        ->select(DB::raw('DATE(completion_date) as date'))
        ->distinct()
        ->orderBy('date', 'asc')
        ->pluck('date')
        ->toArray();

        if (empty($completions)) return 0;

        $maxStreak = 1;
        $currentStreak = 1;

        for ($i = 1; $i < count($completions); $i++) {
            $prev = Carbon::parse($completions[$i - 1]);
            $curr = Carbon::parse($completions[$i]);

            if ($prev->diffInDays($curr) === 1) {
                $currentStreak++;
                $maxStreak = max($maxStreak, $currentStreak);
            } else {
                $currentStreak = 1;
            }
        }

        return $maxStreak;
    }

    /**
     * Calculate overall completion rate.
     */
    private function calculateCompletionRate($user)
    {
        $totalTasks = Task::where('user_id', $user->id)->count();
        $completedTasks = Task::where('user_id', $user->id)
            ->where('status', 'completed')
            ->count();

        return $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0;
    }

    /**
     * Get upcoming tasks (next 7 days).
     */
    private function getUpcomingTasks($user)
    {
        $today = Carbon::today();
        $nextWeek = Carbon::today()->addDays(7);

        return Task::where('user_id', $user->id)
            ->where('status', 'pending')
            ->where(function($query) use ($today, $nextWeek) {
                $query->whereBetween('due_date', [$today, $nextWeek])
                    ->orWhere(function($q) use ($today, $nextWeek) {
                        $q->where('type', 'specific_days')
                          ->whereNotNull('start_date')
                          ->whereBetween('start_date', [$today, $nextWeek]);
                    });
            })
            ->orderBy('due_date', 'asc')
            ->orderBy('start_date', 'asc')
            ->limit(5)
            ->get();
    }

    /**
     * Get recent activity (last 10 completions).
     */
    private function getRecentActivity($user)
    {
        return TaskCompletion::whereHas('task', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->with('task')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Get overall statistics for statistics page.
     */
    private function getOverallStatistics($user)
    {
        $allCompletions = TaskCompletion::whereHas('task', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->get();

        $firstCompletion = $allCompletions->min('completion_date');
        $daysSinceStart = $firstCompletion 
            ? Carbon::parse($firstCompletion)->diffInDays(Carbon::today()) + 1 
            : 0;

        return [
            'total_tasks_created' => Task::where('user_id', $user->id)->count(),
            'active_tasks' => Task::where('user_id', $user->id)->where('status', 'pending')->count(),
            'completed_tasks' => Task::where('user_id', $user->id)->where('status', 'completed')->count(),
            'archived_tasks' => Task::where('user_id', $user->id)->where('status', 'archived')->count(),
            'total_completions' => $allCompletions->count(),
            'days_since_start' => $daysSinceStart,
            'average_completions_per_day' => $daysSinceStart > 0 
                ? round($allCompletions->count() / $daysSinceStart, 1) 
                : 0,
            'most_productive_day' => $this->getMostProductiveDay($user),
            'best_month' => $this->getBestMonth($user),
        ];
    }

    /**
     * Get statistics by category.
     */
    private function getCategoryStatistics($user)
    {
        return Task::where('user_id', $user->id)
            ->select('category', DB::raw('count(*) as total'))
            ->whereNotNull('category')
            ->groupBy('category')
            ->orderBy('total', 'desc')
            ->get()
            ->map(function($item) use ($user) {
                $completions = TaskCompletion::whereHas('task', function($q) use ($user, $item) {
                    $q->where('user_id', $user->id)
                      ->where('category', $item->category);
                })->count();

                return [
                    'category' => $item->category,
                    'total_tasks' => $item->total,
                    'completions' => $completions,
                ];
            });
    }

    /**
     * Get weekly performance data (last 4 weeks).
     */
    private function getWeeklyPerformance($user)
    {
        $weeks = [];
        
        for ($i = 3; $i >= 0; $i--) {
            $weekStart = Carbon::now()->subWeeks($i)->startOfWeek();
            $weekEnd = Carbon::now()->subWeeks($i)->endOfWeek();
            
            $completions = TaskCompletion::whereHas('task', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->whereBetween('completion_date', [$weekStart, $weekEnd])
            ->count();
            
            $weeks[] = [
                'week' => 'Week ' . $weekStart->format('M d'),
                'completions' => $completions,
            ];
        }
        
        return $weeks;
    }

    /**
     * Get monthly trends (last 6 months).
     */
    private function getMonthlyTrends($user)
    {
        $months = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $monthStart = Carbon::now()->subMonths($i)->startOfMonth();
            $monthEnd = Carbon::now()->subMonths($i)->endOfMonth();
            
            $completions = TaskCompletion::whereHas('task', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->whereBetween('completion_date', [$monthStart, $monthEnd])
            ->count();
            
            $tasksCreated = Task::where('user_id', $user->id)
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->count();
            
            $months[] = [
                'month' => $monthStart->format('M Y'),
                'completions' => $completions,
                'tasks_created' => $tasksCreated,
            ];
        }
        
        return $months;
    }

    /**
     * Get user achievements and milestones.
     */
    private function getAchievements($user)
    {
        $totalCompletions = TaskCompletion::whereHas('task', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->count();

        $currentStreak = $this->calculateStreak($user);
        $longestStreak = $this->calculateLongestStreak($user);

        $achievements = [];

        // Completion milestones
        $completionMilestones = [10, 50, 100, 250, 500, 1000];
        foreach ($completionMilestones as $milestone) {
            if ($totalCompletions >= $milestone) {
                $achievements[] = [
                    'icon' => '🎯',
                    'title' => "$milestone Completions",
                    'description' => "Completed $milestone tasks!",
                    'achieved' => true,
                ];
            }
        }

        // Streak achievements
        $streakMilestones = [7, 14, 30, 60, 100, 365];
        foreach ($streakMilestones as $milestone) {
            if ($longestStreak >= $milestone) {
                $achievements[] = [
                    'icon' => '🔥',
                    'title' => "$milestone Day Streak",
                    'description' => "Maintained a $milestone-day streak!",
                    'achieved' => true,
                ];
            }
        }

        // Task creation achievements
        $totalTasks = Task::where('user_id', $user->id)->count();
        if ($totalTasks >= 10) {
            $achievements[] = [
                'icon' => '📝',
                'title' => 'Task Master',
                'description' => 'Created 10 or more tasks!',
                'achieved' => true,
            ];
        }

        // Category diversity
        $categories = Task::where('user_id', $user->id)
            ->distinct('category')
            ->whereNotNull('category')
            ->count();
        
        if ($categories >= 5) {
            $achievements[] = [
                'icon' => '🌈',
                'title' => 'Well Rounded',
                'description' => 'Tasks in 5 different categories!',
                'achieved' => true,
            ];
        }

        // Perfect week
        $lastWeekStart = Carbon::now()->subWeek()->startOfWeek();
        $lastWeekEnd = Carbon::now()->subWeek()->endOfWeek();
        $lastWeekDays = [];
        
        for ($i = 0; $i < 7; $i++) {
            $day = $lastWeekStart->copy()->addDays($i);
            $hasCompletion = TaskCompletion::whereHas('task', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->whereDate('completion_date', $day)->exists();
            
            $lastWeekDays[] = $hasCompletion;
        }
        
        if (count(array_filter($lastWeekDays)) === 7) {
            $achievements[] = [
                'icon' => '⭐',
                'title' => 'Perfect Week',
                'description' => 'Completed tasks every day last week!',
                'achieved' => true,
            ];
        }

        return $achievements;
    }

    /**
     * Get most productive day of week.
     */
    private function getMostProductiveDay($user)
    {
        $dayStats = TaskCompletion::whereHas('task', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->select(DB::raw('DAYNAME(completion_date) as day_name'), DB::raw('count(*) as total'))
            ->groupBy('day_name')
            ->orderBy('total', 'desc')
            ->first();

        return $dayStats ? $dayStats->day_name : 'N/A';
    }

    /**
     * Get best performing month.
     */
    private function getBestMonth($user)
    {
        $monthStats = TaskCompletion::whereHas('task', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->select(DB::raw('DATE_FORMAT(completion_date, "%Y-%m") as month'), DB::raw('count(*) as total'))
            ->groupBy('month')
            ->orderBy('total', 'desc')
            ->first();

        return $monthStats ? Carbon::parse($monthStats->month . '-01')->format('F Y') : 'N/A';
    }
}