@extends('layouts.app')

@section('title', 'Edit Task - Hassam Todo')

@section('styles')
<style>
    .form-container {
        background: white;
        border-radius: 20px;
        padding: 2.5rem;
        max-width: 800px;
        margin: 0 auto;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    }

    .form-header {
        text-align: center;
        margin-bottom: 2rem;
    }

    .form-header h1 {
        font-size: 2rem;
        color: #333;
        margin-bottom: 0.5rem;
    }

    .form-header p {
        color: #666;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
        color: #333;
        font-size: 0.95rem;
    }

    .form-group label .required {
        color: #dc3545;
    }

    .form-control {
        width: 100%;
        padding: 0.875rem 1rem;
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        font-size: 1rem;
        transition: all 0.3s;
        font-family: 'Inter', sans-serif;
    }

    .form-control:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    textarea.form-control {
        min-height: 100px;
        resize: vertical;
    }

    .radio-group {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1rem;
        margin-top: 0.75rem;
    }

    .radio-option {
        position: relative;
    }

    .radio-option input[type="radio"] {
        position: absolute;
        opacity: 0;
    }

    .radio-option label {
        display: block;
        padding: 1rem;
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s;
        font-weight: 500;
    }

    .radio-option input[type="radio"]:checked + label {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-color: #667eea;
    }

    .radio-option label:hover {
        border-color: #667eea;
        transform: translateY(-2px);
    }

    .priority-options {
        display: flex;
        gap: 1rem;
    }

    .priority-option {
        flex: 1;
    }

    .priority-option input[type="radio"]:checked + label.priority-low {
        background: #28a745;
        border-color: #28a745;
    }

    .priority-option input[type="radio"]:checked + label.priority-medium {
        background: #ffc107;
        border-color: #ffc107;
        color: #333;
    }

    .priority-option input[type="radio"]:checked + label.priority-high {
        background: #dc3545;
        border-color: #dc3545;
    }

    .conditional-field {
        display: none;
        animation: fadeIn 0.3s;
    }

    .conditional-field.active {
        display: block;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .form-actions {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
        justify-content: space-between;
        align-items: center;
    }

    .form-actions-left {
        display: flex;
        gap: 1rem;
    }

    .error-message {
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    @media (max-width: 768px) {
        .form-container {
            padding: 1.5rem;
        }

        .radio-group {
            grid-template-columns: 1fr;
        }

        .priority-options {
            flex-direction: column;
        }

        .form-actions {
            flex-direction: column;
        }

        .form-actions-left {
            flex-direction: column;
            width: 100%;
        }

        .form-actions .btn {
            width: 100%;
        }
    }
</style>
@endsection

@section('content')
<div class="form-container">
    <div class="form-header">
        <h1>✏️ Edit Task</h1>
        <p>Update your task details</p>
    </div>

    <form action="{{ route('tasks.update', $task) }}" method="POST" id="taskForm">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="title">Task Title <span class="required">*</span></label>
            <input type="text" id="title" name="title" class="form-control" 
                   value="{{ old('title', $task->title) }}" required>
            @error('title')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" class="form-control">{{ old('description', $task->description) }}</textarea>
            @error('description')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label>Task Type <span class="required">*</span></label>
            <div class="radio-group">
                <div class="radio-option">
                    <input type="radio" id="type_daily" name="type" value="daily" 
                           {{ old('type', $task->type) == 'daily' ? 'checked' : '' }} required>
                    <label for="type_daily">
                        <i class="fas fa-sync-alt"></i><br>
                        Daily Task
                    </label>
                </div>
                <div class="radio-option">
                    <input type="radio" id="type_specific" name="type" value="specific_days" 
                           {{ old('type', $task->type) == 'specific_days' ? 'checked' : '' }}>
                    <label for="type_specific">
                        <i class="fas fa-calendar-check"></i><br>
                        Period Challenge
                    </label>
                </div>
                <div class="radio-option">
                    <input type="radio" id="type_once" name="type" value="one_time" 
                           {{ old('type', $task->type) == 'one_time' ? 'checked' : '' }}>
                    <label for="type_once">
                        <i class="fas fa-check"></i><br>
                        One-Time Task
                    </label>
                </div>
            </div>
            @error('type')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="conditional-field" id="specificDaysFields">
            <div class="form-group">
                <label for="total_days">Total Days <span class="required">*</span></label>
                <input type="number" id="total_days" name="total_days" class="form-control" 
                       min="1" value="{{ old('total_days', $task->total_days) }}">
                @error('total_days')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label for="start_date">Start Date</label>
                <input type="date" id="start_date" name="start_date" class="form-control" 
                       value="{{ old('start_date', $task->start_date?->format('Y-m-d')) }}">
                @error('start_date')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="conditional-field" id="oneTimeFields">
            <div class="form-group">
                <label for="due_date">Due Date</label>
                <input type="date" id="due_date" name="due_date" class="form-control" 
                       value="{{ old('due_date', $task->due_date?->format('Y-m-d')) }}">
                @error('due_date')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="form-group">
            <label for="category">Category</label>
            <input type="text" id="category" name="category" class="form-control" 
                   value="{{ old('category', $task->category) }}" list="categoryList">
            <datalist id="categoryList">
                <option value="Worship">
                <option value="Health">
                <option value="Work">
                <option value="Personal">
                <option value="Education">
                <option value="Family">
            </datalist>
            @error('category')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="reminder_time">Reminder Time</label>
            <input type="time" id="reminder_time" name="reminder_time" class="form-control" 
                   value="{{ old('reminder_time', $task->reminder_time?->format('H:i')) }}">
            @error('reminder_time')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label>Priority <span class="required">*</span></label>
            <div class="priority-options">
                <div class="priority-option">
                    <input type="radio" id="priority_low" name="priority" value="low" 
                           {{ old('priority', $task->priority) == 'low' ? 'checked' : '' }}>
                    <label for="priority_low" class="priority-low">
                        <i class="fas fa-arrow-down"></i> Low
                    </label>
                </div>
                <div class="priority-option">
                    <input type="radio" id="priority_medium" name="priority" value="medium" 
                           {{ old('priority', $task->priority) == 'medium' ? 'checked' : '' }}>
                    <label for="priority_medium" class="priority-medium">
                        <i class="fas fa-minus"></i> Medium
                    </label>
                </div>
                <div class="priority-option">
                    <input type="radio" id="priority_high" name="priority" value="high" 
                           {{ old('priority', $task->priority) == 'high' ? 'checked' : '' }}>
                    <label for="priority_high" class="priority-high">
                        <i class="fas fa-arrow-up"></i> High
                    </label>
                </div>
            </div>
            @error('priority')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-actions">
            <div class="form-actions-left">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Task
                </button>
                <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
            <form action="{{ route('tasks.destroy', $task) }}" method="POST" 
                  onsubmit="return confirm('Are you sure you want to delete this task?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Delete Task
                </button>
            </form>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const typeRadios = document.querySelectorAll('input[name="type"]');
        const specificDaysFields = document.getElementById('specificDaysFields');
        const oneTimeFields = document.getElementById('oneTimeFields');

        function updateConditionalFields() {
            const selectedType = document.querySelector('input[name="type"]:checked').value;
            
            specificDaysFields.classList.remove('active');
            oneTimeFields.classList.remove('active');

            if (selectedType === 'specific_days') {
                specificDaysFields.classList.add('active');
            } else if (selectedType === 'one_time') {
                oneTimeFields.classList.add('active');
            }
        }

        typeRadios.forEach(radio => {
            radio.addEventListener('change', updateConditionalFields);
        });

        updateConditionalFields();
    });
</script>
@endsection