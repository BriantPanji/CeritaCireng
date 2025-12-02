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
        Schema::create('daily_outlet_report_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_outlet_report')
                ->references('id')->on('daily_outlet_reports')
                ->onDelete('cascade');
            $table->foreignId('id_item')->references('id')->on('items');

            // Snapshot data item
            $table->string('item_name', 64)->comment('Nama item saat snapshot');
            $table->integer('item_cost')->comment('Harga item saat snapshot');
            $table->enum('item_unit', ['pcs', 'gr', 'ml', 'unit']);

            // Data stok
            $table->integer('initial_stock')->default(0)
                ->comment('Stok awal (input manual dari staff)');
            $table->integer('stock_delivered')->default(0)
                ->comment('Jumlah dikirim hari ini (auto dari delivery)');
            $table->integer('stock_returned')->default(0)
                ->comment('Jumlah dikembalikan hari ini (auto dari return)');
            $table->integer('qty_damaged')->default(0)
                ->comment('Jumlah rusak/hilang (input manual)');
            $table->integer('stock_remained')->default(0)
                ->comment('Stok akhir tersisa (input manual)');

            // Data penjualan (INPUT MANUAL oleh staff)
            $table->integer('qty_sold')->default(0)
                ->comment('Jumlah terjual (input manual)');

            // Data pengeluaran
            $table->integer('total_expense')->default(0)
                ->comment('Total pengeluaran untuk item ini');

            $table->timestamps();

            // Prevent duplicate items in same report
            $table->unique(['id_outlet_report', 'id_item']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_outlet_report_items');
    }
};
