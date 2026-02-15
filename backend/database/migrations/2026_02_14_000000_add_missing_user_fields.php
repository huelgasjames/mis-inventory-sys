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
            // Check if columns don't exist before adding them
            if (!Schema::hasColumn('users', 'address')) {
                $table->string('address')->after('role');
            }
            if (!Schema::hasColumn('users', 'birthdate')) {
                $table->date('birthdate')->nullable()->after('address');
            }
            if (!Schema::hasColumn('users', 'contact_person')) {
                $table->string('contact_person')->nullable()->after('birthdate');
            }
            if (!Schema::hasColumn('users', 'contact_number')) {
                $table->string('contact_number')->nullable()->after('contact_person');
            }
            if (!Schema::hasColumn('users', 'department_id')) {
                $table->foreignId('department_id')->nullable()->after('contact_number')->constrained()->onDelete('set null');
                $table->index('department_id');
            }
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('department_id');
                $table->index('is_active');
            }
            if (!Schema::hasColumn('users', 'assigned_assets_count')) {
                $table->integer('assigned_assets_count')->default(0)->after('is_active');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['department_id']);
            $table->dropIndex(['is_active']);
            $table->dropForeign(['department_id']);
            $table->dropColumn([
                'address',
                'birthdate', 
                'contact_person',
                'contact_number',
                'department_id',
                'is_active',
                'assigned_assets_count'
            ]);
        });
    }
};
