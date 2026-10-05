<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');
            $table->string('entity');
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->string('method', 10);
            $table->string('path');
            $table->string('ip', 45)->nullable();
            $table->json('payload')->nullable();
            $table->json('before_data')->nullable();
            $table->json('after_data')->nullable();
            $table->unsignedSmallInteger('status_code')->nullable();
            $table->text('error')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
