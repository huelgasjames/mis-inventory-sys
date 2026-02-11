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
        Schema::create('components', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('brand');
            $table->string('model');
            $table->integer('quantity');
            $table->string('unit');
            $table->string('asset_tag')->nullable();
            $table->enum('status', ['available', 'deployed', 'under_repair', 'disposed'])->default('available');
            $table->text('notes')->nullable();
            $table->enum('component_type', [
                'processor', 'motherboard', 'ram', 'storage', 
                'video_card', 'dvd_rom', 'psu'
            ]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('components');
    }
};
