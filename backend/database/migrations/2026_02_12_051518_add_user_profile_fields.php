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
        Schema::table('users', function (Blueprint $table) {
            $table->text('address')->nullable()->after('role');
            $table->date('birthdate')->nullable()->after('address');
            $table->string('contact_person')->nullable()->after('birthdate');
            $table->string('contact_number')->nullable()->after('contact_person');
            $table->foreignId('department_id')->nullable()->after('contact_number')->constrained()->onDelete('set null');
            $table->boolean('is_active')->default(true)->after('department_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropColumn(['address', 'birthdate', 'contact_person', 'contact_number', 'department_id', 'is_active']);
        });
    }
};
