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
        Schema::table('computers', function (Blueprint $table) {
            $table->string('location')->nullable()->after('assigned_to');
            $table->text('description')->nullable()->after('location');
            $table->enum('status', ['Working', 'Defective', 'Under Repair', 'For Disposal'])->default('Working')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('computers', function (Blueprint $table) {
            $table->dropColumn(['location', 'description']);
            $table->enum('status', ['Working', 'Defective', 'For Disposal'])->default('Working')->change();
        });
    }
};
