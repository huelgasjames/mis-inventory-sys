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
        Schema::create('laboratories', function (Blueprint $table) {
            $table->id();
            $table->string('lab_code')->unique();
            $table->string('lab_name');
            $table->text('location');
            $table->string('building');
            $table->string('floor')->nullable();
            $table->integer('capacity');
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('head_of_lab')->nullable()->constrained('users')->onDelete('set null');
            $table->string('contact_number')->nullable();
            $table->string('email')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laboratories');
    }
};
