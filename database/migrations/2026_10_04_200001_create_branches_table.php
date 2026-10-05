<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branches', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('code');
            $table->string('name');
            $table->string('address')->nullable();
            $table->string('email')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['company_id', 'code']);
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->foreignId('branch_id')->nullable()->after('email')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('branch_id');
        });

        Schema::dropIfExists('branches');
    }
};
