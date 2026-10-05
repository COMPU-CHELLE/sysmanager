<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenances', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->text('description');
            $table->decimal('cost', 12, 2)->nullable();
            $table->date('performed_at');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenances');
    }
};
