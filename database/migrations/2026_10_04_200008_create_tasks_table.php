<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_to_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('status')->default('OPEN')->index();
            $table->string('priority')->nullable();
            $table->timestamp('due_date')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->text('solution')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
