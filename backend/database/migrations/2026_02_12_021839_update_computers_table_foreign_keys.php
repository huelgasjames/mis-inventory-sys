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
            // Add foreign key columns if they don't exist
            if (!Schema::hasColumn('computers', 'processor_id')) {
                $table->foreignId('processor_id')->nullable()->constrained()->onDelete('set null');
            }
            if (!Schema::hasColumn('computers', 'motherboard_id')) {
                $table->foreignId('motherboard_id')->nullable()->constrained()->onDelete('set null');
            }
            if (!Schema::hasColumn('computers', 'ram_id')) {
                $table->foreignId('ram_id')->nullable()->constrained()->onDelete('set null');
            }
            if (!Schema::hasColumn('computers', 'storage_id')) {
                $table->foreignId('storage_id')->nullable()->constrained()->onDelete('set null');
            }
            if (!Schema::hasColumn('computers', 'video_card_id')) {
                $table->foreignId('video_card_id')->nullable()->constrained()->onDelete('set null');
            }
            if (!Schema::hasColumn('computers', 'psu_id')) {
                $table->foreignId('psu_id')->nullable()->constrained()->onDelete('set null');
            }
            if (!Schema::hasColumn('computers', 'dvd_rom_id')) {
                $table->foreignId('dvd_rom_id')->nullable()->constrained()->onDelete('set null');
            }
            
            // Drop old string columns if they exist
            if (Schema::hasColumn('computers', 'processor')) {
                $table->dropColumn('processor');
            }
            if (Schema::hasColumn('computers', 'motherboard')) {
                $table->dropColumn('motherboard');
            }
            if (Schema::hasColumn('computers', 'ram')) {
                $table->dropColumn('ram');
            }
            if (Schema::hasColumn('computers', 'storage')) {
                $table->dropColumn('storage');
            }
            if (Schema::hasColumn('computers', 'video_card')) {
                $table->dropColumn('video_card');
            }
            if (Schema::hasColumn('computers', 'psu')) {
                $table->dropColumn('psu');
            }
            if (Schema::hasColumn('computers', 'dvd_rom')) {
                $table->dropColumn('dvd_rom');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('computers', function (Blueprint $table) {
            // Add back string columns
            $table->string('processor')->nullable();
            $table->string('motherboard')->nullable();
            $table->string('ram')->nullable();
            $table->string('storage')->nullable();
            $table->string('video_card')->nullable();
            $table->string('psu')->nullable();
            $table->string('dvd_rom')->nullable();
            
            // Drop foreign key columns
            $table->dropForeign(['processor_id']);
            $table->dropForeign(['motherboard_id']);
            $table->dropForeign(['ram_id']);
            $table->dropForeign(['storage_id']);
            $table->dropForeign(['video_card_id']);
            $table->dropForeign(['psu_id']);
            $table->dropForeign(['dvd_rom_id']);
            
            $table->dropColumn(['processor_id', 'motherboard_id', 'ram_id', 'storage_id', 'video_card_id', 'psu_id', 'dvd_rom_id']);
        });
    }
};
