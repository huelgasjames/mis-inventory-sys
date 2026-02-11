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
        Schema::table('assets', function (Blueprint $table) {
            $table->string('serial_number')->nullable()->after('asset_tag');
            $table->string('asset_type')->nullable()->after('serial_number');
            $table->string('brand')->nullable()->after('asset_type');
            $table->string('model')->nullable()->after('brand');
            $table->date('purchase_date')->nullable()->after('status');
            $table->date('warranty_expiry')->nullable()->after('purchase_date');
            $table->text('notes')->nullable()->after('assigned_to');
            $table->json('specifications')->nullable()->after('notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn([
                'serial_number',
                'asset_type',
                'brand',
                'model',
                'purchase_date',
                'warranty_expiry',
                'notes',
                'specifications'
            ]);
        });
    }
};
