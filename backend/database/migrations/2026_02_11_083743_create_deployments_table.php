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
        Schema::create('deployments', function (Blueprint $table) {
            $table->id();
            $table->string('deployment_number')->unique();
            $table->string('computer_id');
            $table->string('laboratory_id');
            $table->string('department_id');
            $table->enum('status', ['available', 'deployed', 'under_repair', 'retired'])->default('available');
            $table->date('deployment_date');
            $table->date('return_date')->nullable();
            $table->text('notes')->nullable();
            $table->string('deployed_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deployments');
    }
};
