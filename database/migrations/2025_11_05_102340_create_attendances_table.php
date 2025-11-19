<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_user')->constrained('users')->onDelete('cascade');
            $table->date('attendance_date');
            $table->time('attendance_time')->nullable(); // boleh null sampai user check-in
            $table->enum('status', ['HADIR', 'IZIN', 'SAKIT', 'ABSEN'])->default('ABSEN');
            $table->timestamps();

            $table->unique(['id_user','attendance_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
