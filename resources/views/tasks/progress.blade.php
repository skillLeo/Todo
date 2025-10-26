@extends('layouts.app')

@section('title', 'Task Progress - Hassam Todo')

@section('styles')
<style>
    .progress-container {
        background: white;
        border-radius: 20px;
        padding: 2.5rem;
        max-width: 1000px;
        margin: 0 auto;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    }

    .progress-header {
        margin-bottom: 2rem;
        padding-bottom: 1.5rem;
        border-bottom: 2px solid #f0f0f0;
    }

    .progress-header h1 {
        font-size: 2rem;
        color: #333;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .progress-header p {
        color: #666;
        font-size: 1rem;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.5rem;
        border-radius: 16px;
        text-align: center;
        box-shadow: 0 5px 20px rgba(102, 126, 234, 0.3);
    }

    .stat-card h3 {
        font-size: 2.5rem;
        margin-bottom: 0.5rem;
    }

    .stat-card p {
        font-size: 0.95rem;
        opacity: 0.9;
    }

    .stat-card.green {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        box-shadow: 0 5px 20px rgba(17, 153, 142, 0.3);
    }

    .stat-card.orange {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        box-shadow: 0 5px 20px rgba(240, 147, 251, 0.3);
    }

    .progress-circle-container {
        text-align: center;
        margin: 2rem 0;
        padding: 2rem;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        border-radius: 16px;
    }

    .progress-circle {
        width: 200px;
        height: 200px;
        border-radius: 50%;
        background: conic-gradient(
            #667eea 0deg,
            #764ba2 calc(var(--progress) * 3.6deg),
            #e0e0e0 calc(var(--progress) * 3.6deg)
        );
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        position: relative;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }

    .progress-circle::before {
        content: '';
        position: absolute;
        width: 160px;
        height: 160px;
        border-radius: 50%;
        background: white;
    }

    .progress-circle-value {
        position: relative;
        z-index: 1;
        font-size: 3rem;
        font-weight: 700;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .completions-section {
        margin-top: 2rem;
    }

    .completions-section h2 {
        font-size: 1.5rem;
        color: #333;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .completion-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 1rem;
    }

    .completion-card {
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        padding: 1rem;
        border-radius: 12px;
        text-align: center;
        border-left: 4px solid #28a745;
        transition: transform 0.3s;
    }

    .completion-card:hover {
        transform: scale(1.05);
    }

    .completion-date {
        font-weight: 600;
        color: #155724;
        font-size: 1rem;
        margin-bottom: 0.25rem;
    }

    .completion-day {
        color: #155724;
        font-size: 0.85rem;
        opacity: 0.8;
    }

    .completion-notes {
        margin-top: 0.5rem;
        font-size: 0.85rem;
        color: #155724;
        font-style: italic;
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

    .back-btn {
        margin-top: 2rem;
        text-align: center;
    }

    .motivational-message {
        background: linear-gradient(135deg, #fff9e6 0%, #ffe5b4 100%);
        border-left: 4px solid #ffc107;
        padding: 1.5rem;
        border-radius: 12px;
        margin: 2rem 0;
        text-align: center;
    }

    .motivational-message h3 {
        color: #856404;
        font-size: 1.3rem;
        margin-bottom: 0.5rem;
    }

    .motivational-message p {
        color: #856404;
        font-size: 1rem;
    }

    .calendar-view {
        margin-top: 2rem;
        padding: 1.5rem;
        background: #f8f9fa;
        border-radius: 12px;
    }

    .calendar-view h3 {
        margin-bottom: 1rem;
        color: #333;
    }

    .week-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 0.5rem;
    }

    .day-cell {
        aspect-ratio: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        background: white;
        border: 2px solid #e0e0e0;
        font-weight: 500;
        color: #666;
    }

    .day-cell.completed {
        background: #28a745;
        color: white;
        border-color: #28a745;
    }

    .day-cell.today {
        border-color: #667eea;
        border-width: 3px;
    }

    @media (max-width: 768px) {
        .progress-container {
            padding: 1.5rem;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .completion-grid {
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        }

        .progress-circle {
            width: 150px;
            height: 150px;
        }

        .progress-circle::before {
            width: 120px;
            height: 120px;
        }

        .progress-circle-value {
            font-size: 2rem;
        }
    }
</style>
@endsection

@section('content')
<div class="progress-container">
    <div class="progress-header">
        <h1>
            <i class="fas fa-chart-line"></i>
            {{ $task->title }}
        </h1>
        <p>{{ $task->description }}</p>
    </div>

    @if($task->type === 'specific_days')
        <div class="stats-grid">
            <div class="stat-card">
                <h3>{{ $task->completed_days }}</h3>
                <p>Days Completed</p>
            </div>
            <div class="stat-card green">
                <h3>{{ $task->remaining_days }}</h3>
                <p>Days Remaining</p>
            </div>
            <div class="stat-card orange">
                <h3>{{ $task->total_days }}</h3>
                <p>Total Days</p>
            </div>
        </div>

        <div class="progress-circle-container">
            <div class="progress-circle" style="--progress: {{ $task->progress_percentage }}">
                <span class="progress-circle-value">{{ $task->progress_percentage }}%</span>
            </div>
            <p style="font-size: 1.2rem; color: #333; font-weight: 600;">Overall Progress</p>
        </div>

        @if($task->progress_percentage >= 75)
            <div class="motivational-message">
                <h3>🎉 Amazing Progress!</h3>
                <p>You're doing great! Keep up the excellent work. You're almost there!</p>
            </div>
        @elseif($task->progress_percentage >= 50)
            <div class="motivational-message">
                <h3>💪 Halfway There!</h3>
                <p>You've crossed the halfway mark! Stay consistent and keep pushing forward!</p>
            </div>
        @elseif($task->progress_percentage >= 25)
            <div class="motivational-message">
                <h3>🚀 Good Start!</h3>
                <p>You're building momentum! Every day counts. Keep going strong!</p>
            </div>
        @else
            <div class="motivational-message">
                <h3>🌟 Begin Your Journey!</h3>
                <p>Every great achievement starts with a single step. You've got this!</p>
            </div>
        @endif
    @endif

    <div class="completions-section">
        <h2>
            <i class="fas fa-calendar-check"></i>
            Completion History
        </h2>

        @if($completions->count() > 0)
            <div class="completion-grid">
                @foreach($completions as $completion)
                    <div class="completion-card">
                        <div class="completion-date">
                            {{ $completion->completion_date->format('M d, Y') }}
                        </div>
                        <div class="completion-day">
                            {{ $completion->completion_date->format('l') }}
                        </div>
                        @if($completion->notes)
                            <div class="completion-notes">
                                "{{ $completion->notes }}"
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <div style="margin-top: 2rem; text-align: center;">
                {{ $completions->links() }}
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-calendar-plus"></i>
                <p>No completions yet. Start marking your progress!</p>
            </div>
        @endif
    </div>

    <div class="back-btn">
        <a href="{{ route('dashboard') }}" class="btn btn-primary">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>
</div>
@endsection