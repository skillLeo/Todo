@extends('layouts.app')

@section('title', 'Dashboard - Hassam Todo')

@section('styles')
<style>
    .dashboard-header {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }

    .dashboard-header h1 {
        font-size: 2rem;
        color: #333;
        margin-bottom: 0.5rem;
    }

    .dashboard-header p {
        color: #666;
        font-size: 1.1rem;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        padding: 2rem;
        border-radius: 16px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        display: flex;
        align-items: center;
        gap: 1.5rem;
        transition: transform 0.3s;
    }

    .stat-card:hover {
        transform: translateY(-5px);
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .stat-icon.purple {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .stat-icon.green {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        color: white;
    }

    .stat-icon.orange {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
    }

    .stat-content h3 {
        font-size: 2rem;
        color: #333;
        margin-bottom: 0.25rem;
    }

    .stat-content p {
        color: #666;
        font-size: 0.9rem;
    }

    .section {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #f0f0f0;
    }

    .section-header h2 {
        font-size: 1.5rem;
        color: #333;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .task-card {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.3s;
        border-left: 4px solid transparent;
    }

    .task-card:hover {
        transform: translateX(5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .task-card.priority-high {
        border-left-color: #dc3545;
    }

    .task-card.priority-medium {
        border-left-color: #ffc107;
    }

    .task-card.priority-low {
        border-left-color: #28a745;
    }

    .task-card.completed-today {
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        border-left-color: #28a745;
    }

    .task-info {
        flex: 1;
    }

    .task-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #333;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .task-meta {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        align-items: center;
        color: #666;
        font-size: 0.9rem;
    }

    .badge {
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .badge-daily {
        background: #e3f2fd;
        color: #1976d2;
    }

    .badge-specific {
        background: #fff3e0;
        color: #f57c00;
    }

    .badge-category {
        background: #f3e5f5;
        color: #7b1fa2;
    }

    .progress-bar-container {
        width: 200px;
        height: 8px;
        background: #e0e0e0;
        border-radius: 10px;
        overflow: hidden;
    }

    .progress-bar {
        height: 100%;
        background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        border-radius: 10px;
        transition: width 0.3s;
    }

    .task-actions {
        display: flex;
        gap: 0.5rem;
    }

    .btn-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        cursor: pointer;
        transition: all 0.3s;
        font-size: 1rem;
    }

    .btn-complete {
        background: #28a745;
        color: white;
    }

    .btn-complete:hover {
        background: #218838;
        transform: scale(1.1);
    }

    .btn-edit {
        background: #667eea;
        color: white;
    }

    .btn-view {
        background: #17a2b8;
        color: white;
    }

    .empty-state {
        text-align: center;
        padding: 3rem;
        color: #999;
    }

    .empty-state i {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.3;
    }

    .empty-state p {
        font-size: 1.1rem;
    }

    @media (max-width: 768px) {
        .task-card {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }

        .task-actions {
            width: 100%;
            justify-content: flex-end;
        }

        .progress-bar-container {
            width: 100%;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
<div class="dashboard-header">
    <h1>Welcome back, {{ auth()->user()->name }}! 👋</h1>
    <p>Here's your task overview for today</p>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon purple">
            <i class="fas fa-tasks"></i>
        </div>
        <div class="stat-content">
            <h3>{{ $stats['total_tasks'] }}</h3>
            <p>Active Tasks</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon green">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-content">
            <h3>{{ $stats['completed_today'] }}</h3>
            <p>Completed Today</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon orange">
            <i class="fas fa-trophy"></i>
        </div>
        <div class="stat-content">
            <h3>{{ $stats['total_completed'] }}</h3>
            <p>Total Completed</p>
        </div>
    </div>
</div>

<!-- Daily Tasks Section -->
<div class="section">
    <div class="section-header">
        <h2>
            <i class="fas fa-calendar-day"></i>
            Daily Tasks
        </h2>
        <a href="{{ route('tasks.create') }}?type=daily" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Daily Task
        </a>
    </div>

    @forelse($dailyTasks as $task)
        <div class="task-card priority-{{ $task->priority }} {{ $task->isCompletedToday() ? 'completed-today' : '' }}">
            <div class="task-info">
                <div class="task-title">
                    @if($task->isCompletedToday())
                        <i class="fas fa-check-circle" style="color: #28a745;"></i>
                    @endif
                    {{ $task->title }}
                </div>
                <div class="task-meta">
                    <span class="badge badge-daily">Daily</span>
                    @if($task->category)
                        <span class="badge badge-category">
                            <i class="fas fa-tag"></i> {{ $task->category }}
                        </span>
                    @endif
                    @if($task->reminder_time)
                        <span>
                            <i class="fas fa-clock"></i> {{ $task->reminder_time->format('h:i A') }}
                        </span>
                    @endif
                </div>
            </div>
            <div class="task-actions">
                @if($task->isCompletedToday())
                    <form action="{{ route('tasks.uncomplete', $task) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-icon btn-complete" title="Mark as incomplete">
                            <i class="fas fa-undo"></i>
                        </button>
                    </form>
                @else
                    <form action="{{ route('tasks.complete', $task) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-icon btn-complete" title="Mark as complete">
                            <i class="fas fa-check"></i>
                        </button>
                    </form>
                @endif
                <a href="{{ route('tasks.edit', $task) }}" class="btn-icon btn-edit" title="Edit">
                    <i class="fas fa-edit"></i>
                </a>
            </div>
        </div>
    @empty
        <div class="empty-state">
            <i class="fas fa-calendar-day"></i>
            <p>No daily tasks yet. Create your first daily task to build consistency!</p>
        </div>
    @endforelse
</div>

<!-- Specific Days Tasks Section -->
<div class="section">
    <div class="section-header">
        <h2>
            <i class="fas fa-calendar-alt"></i>
            Specific Period Tasks (40 Days, etc.)
        </h2>
        <a href="{{ route('tasks.create') }}?type=specific_days" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Period Task
        </a>
    </div>

    @forelse($specificDaysTasks as $task)
        <div class="task-card priority-{{ $task->priority }} {{ $task->isCompletedToday() ? 'completed-today' : '' }}">
            <div class="task-info">
                <div class="task-title">
                    @if($task->isCompletedToday())
                        <i class="fas fa-check-circle" style="color: #28a745;"></i>
                    @endif
                    {{ $task->title }}
                </div>
                <div class="task-meta">
                    <span class="badge badge-specific">{{ $task->total_days }} Days Challenge</span>
                    @if($task->category)
                        <span class="badge badge-category">
                            <i class="fas fa-tag"></i> {{ $task->category }}
                        </span>
                    @endif
                    <span>
                        <i class="fas fa-fire"></i> {{ $task->completed_days }}/{{ $task->total_days }} days
                    </span>
                    <div class="progress-bar-container">
                        <div class="progress-bar" style="width: {{ $task->progress_percentage }}%"></div>
                    </div>
                    <span style="font-weight: 600; color: #667eea;">{{ $task->progress_percentage }}%</span>
                </div>
            </div>
            <div class="task-actions">
                @if($task->isCompletedToday())
                    <form action="{{ route('tasks.uncomplete', $task) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-icon btn-complete" title="Mark as incomplete">
                            <i class="fas fa-undo"></i>
                        </button>
                    </form>
                @else
                    <form action="{{ route('tasks.complete', $task) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-icon btn-complete" title="Mark as complete">
                            <i class="fas fa-check"></i>
                        </button>
                    </form>
                @endif
                <a href="{{ route('tasks.progress', $task) }}" class="btn-icon btn-view" title="View Progress">
                    <i class="fas fa-chart-line"></i>
                </a>
                <a href="{{ route('tasks.edit', $task) }}" class="btn-icon btn-edit" title="Edit">
                    <i class="fas fa-edit"></i>
                </a>
            </div>
        </div>
    @empty
        <div class="empty-state">
            <i class="fas fa-calendar-check"></i>
            <p>No period tasks yet. Start a 40-day challenge or any custom period task!</p>
        </div>
    @endforelse
</div>

<!-- One-Time Tasks Section -->
<div class="section">
    <div class="section-header">
        <h2>
            <i class="fas fa-clipboard-list"></i>
            One-Time Tasks
        </h2>
        <a href="{{ route('tasks.create') }}?type=one_time" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Task
        </a>
    </div>

    @forelse($oneTimeTasks as $task)
        <div class="task-card priority-{{ $task->priority }}">
            <div class="task-info">
                <div class="task-title">
                    {{ $task->title }}
                </div>
                <div class="task-meta">
                    @if($task->category)
                        <span class="badge badge-category">
                            <i class="fas fa-tag"></i> {{ $task->category }}
                        </span>
                    @endif
                    @if($task->due_date)
                        <span>
                            <i class="fas fa-calendar"></i> Due: {{ $task->due_date->format('M d, Y') }}
                        </span>
                    @endif
                    @if($task->reminder_time)
                        <span>
                            <i class="fas fa-clock"></i> {{ $task->reminder_time->format('h:i A') }}
                        </span>
                    @endif
                </div>
            </div>
            <div class="task-actions">
                <form action="{{ route('tasks.complete', $task) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-icon btn-complete" title="Mark as complete">
                        <i class="fas fa-check"></i>
                    </button>
                </form>
                <a href="{{ route('tasks.edit', $task) }}" class="btn-icon btn-edit" title="Edit">
                    <i class="fas fa-edit"></i>
                </a>
            </div>
        </div>
    @empty
        <div class="empty-state">
            <i class="fas fa-clipboard"></i>
            <p>No one-time tasks. Add tasks that need to be done once!</p>
        </div>
    @endforelse
</div>
@endsection