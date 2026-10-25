<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Tenants (Mitra/Penyewa)
        Schema::create('tenants', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nama_mitra', 255);
            $table->string('alamat_mitra', 500)->nullable();
            $table->string('status_mitra', 100)->nullable()->default('Swasta');
            $table->timestamp('created_at')->useCurrent();
        });

        // 2. Tabel Assets
        Schema::create('assets', function (Blueprint $table) {
            $table->string('nomor_aset', 100)->primary();
            $table->string('blok_aset', 255)->nullable();
            $table->string('jenis_aset', 100)->nullable()->default('Tanah');
            $table->string('peruntukan', 255)->nullable();
            $table->decimal('luas_tanah', 12, 2)->nullable()->default(0);
            $table->decimal('luas_bangunan', 12, 2)->nullable()->default(0);
            $table->string('kmhm', 100)->nullable();   // Kilometer/Hekto-Meter lokasi aset
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        // 3. Tabel Contracts
        Schema::create('contracts', function (Blueprint $table) {
            $table->string('nomor_kontrak', 150)->primary();
            $table->unsignedInteger('tenant_id');
            $table->string('nomor_aset', 100)->nullable();
            $table->string('tanggal_kontrak', 50)->nullable();   // Disimpan as string karena format beragam
            $table->string('kelompok', 100)->nullable();          // Non Row / Row / dll
            $table->date('masa_awal')->nullable();
            $table->date('masa_akhir')->nullable();
            $table->decimal('nilai_kontrak', 15, 2)->default(0);
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('tenant_id')
                ->references('id')->on('tenants')
                ->cascadeOnDelete()->cascadeOnUpdate();

            $table->foreign('nomor_aset')
                ->references('nomor_aset')->on('assets')
                ->nullOnDelete()->cascadeOnUpdate();
        });

        // 4. Tabel Payment Records (Data Tagihan per Termin)
        Schema::create('payment_records', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_kontrak', 150);
            $table->integer('termin_ke')->default(1);
            $table->string('virtual_account', 100)->nullable();
            $table->date('tanggal_termin')->nullable();
            $table->decimal('total_bayar', 15, 2)->default(0);
            $table->string('invoice_id', 150)->nullable();
            $table->string('transaction_id', 150)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('nomor_kontrak')
                ->references('nomor_kontrak')->on('contracts')
                ->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_records');
        Schema::dropIfExists('contracts');
        Schema::dropIfExists('assets');
        Schema::dropIfExists('tenants');
    }
};
