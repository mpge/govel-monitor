<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('govel_task_executions', function (Blueprint $table) {
            $table->id();
            $table->string('task')->index();
            $table->string('driver');
            $table->string('mode'); // sync, async, queued
            $table->json('payload')->nullable();
            $table->boolean('success')->nullable();
            $table->json('output')->nullable();
            $table->text('error')->nullable();
            $table->float('duration')->nullable(); // milliseconds
            $table->timestamp('executed_at')->index();
            $table->timestamps();

            $table->index(['task', 'success']);
            $table->index(['executed_at', 'success']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('govel_task_executions');
    }
};
