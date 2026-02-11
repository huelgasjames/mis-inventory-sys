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
        Schema::create('computers', function (Blueprint $table) {
            $table->id();
            $table->string('computer_name');
            $table->string('asset_tag')->unique();
            $table->string('processor')->nullable();
            $table->string('motherboard')->nullable();
            $table->string('ram')->nullable();
            $table->string('storage')->nullable();
            $table->string('video_card')->nullable();
            $table->string('psu')->nullable();
            $table->string('dvd_rom')->nullable();
            $table->enum('status', ['Working', 'Defective', 'For Disposal'])->default('Working');
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('computers');
    }
};
