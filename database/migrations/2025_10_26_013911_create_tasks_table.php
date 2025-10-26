<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['daily', 'specific_days', 'one_time'])->default('one_time');
            $table->integer('total_days')->nullable(); // For specific_days type (e.g., 40 days)
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->time('reminder_time')->nullable();
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->enum('status', ['pending', 'completed', 'archived'])->default('pending');
            $table->string('category')->nullable(); // e.g., 'worship', 'health', 'work'
            $table->timestamps();
        });

        Schema::create('task_completions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->onDelete('cascade');
            $table->date('completion_date');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['task_id', 'completion_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_completions');
        Schema::dropIfExists('tasks');
    }
};