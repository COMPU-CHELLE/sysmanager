<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('type');
            $table->string('serial')->nullable()->index();
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->date('purchase_date')->nullable();
            $table->decimal('cost', 12, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('asset_details', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('asset_id')->unique()->constrained()->cascadeOnDelete();

            foreach ([
                'ram_type', 'ram_capacity', 'hdd_type', 'hdd_capacity', 'pro_type', 'pro_detail',
                'mbr_type', 'mbr_detail', 'gra_type', 'gra_detail', 'monitor', 'mon_detail',
                'keyboard', 'key_detail', 'mouse', 'mou_detail',
            ] as $column) {
                $table->string($column)->nullable();
            }

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_details');
        Schema::dropIfExists('assets');
    }
};
