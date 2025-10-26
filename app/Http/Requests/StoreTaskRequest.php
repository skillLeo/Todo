<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type' => 'required|in:daily,specific_days,one_time',
            'priority' => 'required|in:low,medium,high',
            'category' => 'nullable|string|max:100',
            'reminder_time' => 'nullable|date_format:H:i',
        ];

        // Add conditional validation based on task type
        if ($this->input('type') === 'specific_days') {
            $rules['total_days'] = 'required|integer|min:1|max:365';
            $rules['start_date'] = 'nullable|date|after_or_equal:today';
        }

        if ($this->input('type') === 'one_time') {
            $rules['due_date'] = 'nullable|date|after_or_equal:today';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Please enter a task title.',
            'title.max' => 'Task title cannot be longer than 255 characters.',
            'type.required' => 'Please select a task type.',
            'type.in' => 'Invalid task type selected.',
            'total_days.required' => 'Total days is required for period challenges.',
            'total_days.min' => 'Period challenge must be at least 1 day.',
            'total_days.max' => 'Period challenge cannot exceed 365 days.',
            'priority.required' => 'Please select a priority level.',
            'start_date.after_or_equal' => 'Start date cannot be in the past.',
            'due_date.after_or_equal' => 'Due date cannot be in the past.',
            'reminder_time.date_format' => 'Invalid time format. Use HH:MM format.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set default start date for specific_days if not provided
        if ($this->input('type') === 'specific_days' && !$this->has('start_date')) {
            $this->merge([
                'start_date' => now()->format('Y-m-d'),
            ]);
        }
    }
}

class UpdateTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->id === $this->route('task')->user_id;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type' => 'required|in:daily,specific_days,one_time',
            'priority' => 'required|in:low,medium,high',
            'category' => 'nullable|string|max:100',
            'reminder_time' => 'nullable|date_format:H:i',
        ];

        // Add conditional validation based on task type
        if ($this->input('type') === 'specific_days') {
            $rules['total_days'] = 'required|integer|min:1|max:365';
            $rules['start_date'] = 'nullable|date';
        }

        if ($this->input('type') === 'one_time') {
            $rules['due_date'] = 'nullable|date';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Please enter a task title.',
            'title.max' => 'Task title cannot be longer than 255 characters.',
            'type.required' => 'Please select a task type.',
            'type.in' => 'Invalid task type selected.',
            'total_days.required' => 'Total days is required for period challenges.',
            'total_days.min' => 'Period challenge must be at least 1 day.',
            'total_days.max' => 'Period challenge cannot exceed 365 days.',
            'priority.required' => 'Please select a priority level.',
            'reminder_time.date_format' => 'Invalid time format. Use HH:MM format.',
        ];
    }
}