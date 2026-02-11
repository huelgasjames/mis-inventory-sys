<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('asset_tag')->unique();
            $table->string('serial_number')->nullable();
            $table->string('asset_type')->nullable();
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('description')->nullable();
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('status', ['Working', 'Defective', 'Under Repair', 'For Disposal'])->default('Working');
            $table->date('purchase_date')->nullable();
            $table->date('warranty_expiry')->nullable();
            $table->string('location')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->json('specifications')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
