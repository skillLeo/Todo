<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('task_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('date'); // the day this habit/todo instance was done
            $table->boolean('completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->text('note')->nullable();

            $table->timestamps();
            $table->unique(['task_id', 'date']); // no duplicates per day
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_logs');
    }
};
