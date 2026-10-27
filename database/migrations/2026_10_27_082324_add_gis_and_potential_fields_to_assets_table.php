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
            if (! Schema::hasColumn('assets', 'stasiun')) {
                $table->string('stasiun', 100)->nullable()->after('kmhm');
            }
            if (! Schema::hasColumn('assets', 'wilayah_asset')) {
                $table->string('wilayah_asset', 100)->nullable()->default('Daop 4 Semarang')->after('stasiun');
            }
            if (! Schema::hasColumn('assets', 'status_potensi')) {
                $table->string('status_potensi', 50)->nullable()->default('potensial')->after('wilayah_asset');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            if (Schema::hasColumn('assets', 'status_potensi')) {
                $table->dropColumn('status_potensi');
            }
            if (Schema::hasColumn('assets', 'wilayah_asset')) {
                $table->dropColumn('wilayah_asset');
            }
            if (Schema::hasColumn('assets', 'stasiun')) {
                $table->dropColumn('stasiun');
            }
        });
    }
};
