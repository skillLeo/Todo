<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskCompletion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TaskController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of tasks (Dashboard)
     */
    public function index()
    {
        $user = Auth::user();
        
        $dailyTasks = Task::where('user_id', $user->id)
            ->where('type', 'daily')
            ->where('status', 'pending')
            ->orderBy('priority', 'desc')
            ->orderBy('reminder_time', 'asc')
            ->get();

        $specificDaysTasks = Task::where('user_id', $user->id)
            ->where('type', 'specific_days')
            ->where('status', 'pending')
            ->orderBy('priority', 'desc')
            ->get();

        $oneTimeTasks = Task::where('user_id', $user->id)
            ->where('type', 'one_time')
            ->where('status', 'pending')
            ->orderBy('due_date', 'asc')
            ->orderBy('priority', 'desc')
            ->get();

        // Statistics
        $stats = $this->getUserStatistics($user);

        return view('tasks.index', compact('dailyTasks', 'specificDaysTasks', 'oneTimeTasks', 'stats'));
    }

    /**
     * Show the form for creating a new task.
     */
    public function create()
    {
        $categories = $this->getCategories();
        return view('tasks.create', compact('categories'));
    }

    /**
     * Store a newly created task in database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type' => 'required|in:daily,specific_days,one_time',
            'total_days' => 'nullable|integer|min:1|max:365',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'reminder_time' => 'nullable|date_format:H:i',
            'priority' => 'required|in:low,medium,high',
            'category' => 'nullable|string|max:100',
        ]);

        // Validation logic based on task type
        if ($validated['type'] === 'specific_days' && !$request->has('total_days')) {
            return back()->withErrors(['total_days' => 'Total days is required for period challenges.'])->withInput();
        }

        // Set default start date for specific_days tasks
        if ($validated['type'] === 'specific_days' && !$request->has('start_date')) {
            $validated['start_date'] = Carbon::today();
        }

        $validated['user_id'] = Auth::id();
        $validated['status'] = 'pending';

        try {
            $task = Task::create($validated);
            
            return redirect()
                ->route('tasks.index')
                ->with('success', '✅ Task created successfully! Start building your habit now.');
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Failed to create task. Please try again.'])
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified task.
     */
    public function edit(Task $task)
    {
        $this->authorize('update', $task);
        $categories = $this->getCategories();
        return view('tasks.edit', compact('task', 'categories'));
    }

    /**
     * Update the specified task in database.
     */
    public function update(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type' => 'required|in:daily,specific_days,one_time',
            'total_days' => 'nullable|integer|min:1|max:365',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'reminder_time' => 'nullable|date_format:H:i',
            'priority' => 'required|in:low,medium,high',
            'category' => 'nullable|string|max:100',
        ]);

        try {
            $task->update($validated);
            
            return redirect()
                ->route('tasks.index')
                ->with('success', '✅ Task updated successfully!');
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Failed to update task. Please try again.'])
                ->withInput();
        }
    }

    /**
     * Remove the specified task from database.
     */
    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);

        try {
            $task->delete();
            
            return redirect()
                ->route('tasks.index')
                ->with('success', '🗑️ Task deleted successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to delete task. Please try again.']);
        }
    }

    /**
     * Mark task as complete for today.
     */
    public function complete(Task $task)
    {
        $this->authorize('update', $task);

        try {
            DB::beginTransaction();

            $completion = TaskCompletion::firstOrCreate([
                'task_id' => $task->id,
                'completion_date' => Carbon::today(),
            ]);

            // Check if specific_days task is completed
            if ($task->type === 'specific_days' && $task->completed_days >= $task->total_days) {
                $task->update(['status' => 'completed']);
            }

            DB::commit();

            return redirect()
                ->back()
                ->with('success', '🎉 Great job! Task marked as completed for today!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to complete task. Please try again.']);
        }
    }

    /**
     * Unmark task completion for today.
     */
    public function uncomplete(Task $task)
    {
        $this->authorize('update', $task);

        try {
            DB::beginTransaction();

            TaskCompletion::where('task_id', $task->id)
                ->whereDate('completion_date', Carbon::today())
                ->delete();

            // Reopen task if it was marked as completed
            if ($task->status === 'completed') {
                $task->update(['status' => 'pending']);
            }

            DB::commit();

            return redirect()
                ->back()
                ->with('success', '↩️ Task completion removed for today.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to remove completion. Please try again.']);
        }
    }

    /**
     * Show task progress and completion history.
     */
    public function progress(Task $task)
    {
        $this->authorize('view', $task);

        $completions = $task->completions()
            ->orderBy('completion_date', 'desc')
            ->paginate(30);

        // Get weekly completion data
        $weeklyData = $this->getWeeklyCompletionData($task);

        // Get monthly stats
        $monthlyStats = $this->getMonthlyStats($task);

        return view('tasks.progress', compact('task', 'completions', 'weeklyData', 'monthlyStats'));
    }

    /**
     * Archive a task.
     */
    public function archive(Task $task)
    {
        $this->authorize('update', $task);

        try {
            $task->update(['status' => 'archived']);
            
            return redirect()
                ->back()
                ->with('success', '📦 Task archived successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to archive task.']);
        }
    }

    /**
     * Show archived tasks.
     */
    public function archived()
    {
        $user = Auth::user();
        
        $archivedTasks = Task::where('user_id', $user->id)
            ->where('status', 'archived')
            ->orderBy('updated_at', 'desc')
            ->paginate(20);

        return view('tasks.archived', compact('archivedTasks'));
    }

    /**
     * Restore archived task.
     */
    public function restore(Task $task)
    {
        $this->authorize('update', $task);

        try {
            $task->update(['status' => 'pending']);
            
            return redirect()
                ->route('tasks.index')
                ->with('success', '♻️ Task restored successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to restore task.']);
        }
    }

    /**
     * Add notes to task completion.
     */
    public function addCompletionNote(Request $request, Task $task, TaskCompletion $completion)
    {
        $this->authorize('update', $task);

        $validated = $request->validate([
            'notes' => 'required|string|max:500',
        ]);

        try {
            $completion->update(['notes' => $validated['notes']]);
            
            return redirect()
                ->back()
                ->with('success', '📝 Notes added successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to add notes.']);
        }
    }

    /**
     * Bulk complete tasks.
     */
    public function bulkComplete(Request $request)
    {
        $validated = $request->validate([
            'task_ids' => 'required|array',
            'task_ids.*' => 'exists:tasks,id',
        ]);

        try {
            DB::beginTransaction();

            foreach ($validated['task_ids'] as $taskId) {
                $task = Task::find($taskId);
                
                if ($task && $task->user_id === Auth::id()) {
                    TaskCompletion::firstOrCreate([
                        'task_id' => $task->id,
                        'completion_date' => Carbon::today(),
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->back()
                ->with('success', '✅ Multiple tasks completed successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to complete tasks.']);
        }
    }

    /**
     * Bulk delete tasks.
     */
    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'task_ids' => 'required|array',
            'task_ids.*' => 'exists:tasks,id',
        ]);

        try {
            Task::whereIn('id', $validated['task_ids'])
                ->where('user_id', Auth::id())
                ->delete();

            return redirect()
                ->back()
                ->with('success', '🗑️ Tasks deleted successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to delete tasks.']);
        }
    }

    /**
     * Get user statistics.
     */
    private function getUserStatistics($user)
    {
        $today = Carbon::today();
        $weekStart = Carbon::now()->startOfWeek();
        $monthStart = Carbon::now()->startOfMonth();

        return [
            'total_tasks' => Task::where('user_id', $user->id)->where('status', 'pending')->count(),
            'completed_today' => TaskCompletion::whereHas('task', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->whereDate('completion_date', $today)->count(),
            'total_completed' => TaskCompletion::whereHas('task', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->count(),
            'completed_this_week' => TaskCompletion::whereHas('task', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->whereBetween('completion_date', [$weekStart, $today])->count(),
            'completed_this_month' => TaskCompletion::whereHas('task', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->whereBetween('completion_date', [$monthStart, $today])->count(),
            'streak' => $this->calculateStreak($user),
        ];
    }

    /**
     * Calculate user's current streak.
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
                break;
            }
        }

        return $streak;
    }

    /**
     * Get weekly completion data.
     */
    private function getWeeklyCompletionData($task)
    {
        $weekStart = Carbon::now()->startOfWeek();
        $data = [];

        for ($i = 0; $i < 7; $i++) {
            $date = $weekStart->copy()->addDays($i);
            $completed = $task->completions()
                ->whereDate('completion_date', $date)
                ->exists();
            
            $data[] = [
                'date' => $date,
                'day' => $date->format('D'),
                'completed' => $completed,
                'is_today' => $date->isToday(),
            ];
        }

        return $data;
    }

    /**
     * Get monthly statistics.
     */
    private function getMonthlyStats($task)
    {
        $monthStart = Carbon::now()->startOfMonth();
        $today = Carbon::today();

        $completionsThisMonth = $task->completions()
            ->whereBetween('completion_date', [$monthStart, $today])
            ->count();

        $daysInMonth = $today->diffInDays($monthStart) + 1;

        return [
            'completions' => $completionsThisMonth,
            'days_in_month' => $daysInMonth,
            'percentage' => $daysInMonth > 0 ? round(($completionsThisMonth / $daysInMonth) * 100, 1) : 0,
        ];
    }

    /**
     * Get available categories.
     */
    private function getCategories()
    {
        return [
            'Worship' => '🕌 Worship',
            'Health' => '💪 Health',
            'Work' => '💼 Work',
            'Personal' => '👤 Personal',
            'Education' => '📚 Education',
            'Family' => '👨‍👩‍👧‍👦 Family',
            'Finance' => '💰 Finance',
            'Hobbies' => '🎨 Hobbies',
            'Other' => '📌 Other',
        ];
    }
}