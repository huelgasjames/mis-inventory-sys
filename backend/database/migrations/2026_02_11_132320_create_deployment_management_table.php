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
        Schema::create('deployment_management', function (Blueprint $table) {
            $table->id();
            $table->foreignId('computer_id')->constrained()->onDelete('cascade');
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('location')->nullable();
            $table->enum('status', ['deployed', 'returned', 'maintenance', 'retired'])->default('deployed');
            $table->date('deployment_date');
            $table->date('return_date')->nullable();
            $table->text('notes')->nullable();
            $table->string('deployed_by');
            $table->timestamps();
            
            $table->index(['computer_id', 'department_id']);
            $table->index(['status', 'deployment_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deployment_management');
    }
};
