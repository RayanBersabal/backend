<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Informasi dari form checkout
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->text('delivery_address');
            $table->text('notes')->nullable();

            // Informasi Pembayaran dan Harga (Perhatikan Tipe Data unsignedInteger sesuai product price Anda)
            $table->string('payment_type');
            $table->unsignedInteger('subtotal');
            $table->unsignedInteger('delivery_fee');
            $table->unsignedInteger('admin_fee');
            $table->unsignedInteger('total_amount');

            // --- KOLOM BARU UNTUK STATUS PEMBAYARAN DAN PESANAN ---
            $table->string('payment_status')->default('pending'); // Status pembayaran: pending, paid, failed
            $table->string('payment_reference')->nullable();     // ID transaksi atau referensi pembayaran
            $table->timestamp('paid_at')->nullable();           // Waktu pembayaran dikonfirmasi
            // --- AKHIR KOLOM BARU ---

            // Status dan Informasi Tambahan (kolom 'status' yang sudah ada)
            $table->string('status')->default('Dipesan'); // Status pesanan: Dipesan, Disiapkan, Dikirim, Pesanan Selesai
            $table->string('estimated_delivery_time')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
