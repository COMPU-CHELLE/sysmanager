<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_assignments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamp('returned_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('asset_assignment_documents', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('asset_assignment_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('document_number');
            $table->timestamp('generated_at')->useCurrent();
            $table->foreignId('generated_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_assignment_documents');
        Schema::dropIfExists('asset_assignments');
    }
};
