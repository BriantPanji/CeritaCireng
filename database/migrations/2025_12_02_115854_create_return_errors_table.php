<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('return_errors', function (Blueprint $table) {
            $table->id();

            $table->foreignId('id_return')
                ->constrained('returns')
                ->onDelete('cascade');

            $table->foreignId('id_item')
                ->constrained('items');

            $table->foreignId('id_staff')
                ->constrained('users');

            $table->integer('wrong_quantity')->default(1);

            $table->string('reason', 1024)->nullable();

            $table->string('photo_path');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('return_errors');
    }
};
