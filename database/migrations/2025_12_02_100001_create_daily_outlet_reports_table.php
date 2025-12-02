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
        Schema::create('daily_outlet_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_outlet')->references('id')->on('outlets');
            $table->foreignId('id_staff')->references('id')->on('users')
                ->comment('Staff yang membuat laporan');
            $table->date('report_date')->comment('Tanggal laporan');
            $table->time('report_time')->comment('Waktu pembuatan laporan');
            $table->boolean('is_validated')->default(true)
                ->comment('Status validasi: false jika data sumber telah berubah');

            // Metadata snapshot
            $table->text('notes')->nullable()->comment('Catatan tambahan dari staff');
            $table->string('created_by_name', 128)->comment('Nama staff saat laporan dibuat');
            $table->string('outlet_name', 128)->comment('Nama outlet saat laporan dibuat');

            $table->timestamps();

            // Constraint: 1 laporan per outlet per hari
            $table->unique(['id_outlet', 'report_date'], 'unique_outlet_daily_report');

            // Index untuk performa
            $table->index(['id_outlet', 'report_date']);
            $table->index('is_validated');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_outlet_reports');
    }
};
