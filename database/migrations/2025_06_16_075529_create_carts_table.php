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
        Schema::create('carts', function (Blueprint $table) {
            $table->id();

            // Kolom ini menghubungkan ke tabel 'users' untuk tahu siapa pemilik keranjang ini.
            // onDelete('cascade') artinya jika user dihapus, semua isi keranjangnya juga ikut terhapus.
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Kolom ini menghubungkan ke tabel 'products' untuk tahu produk apa yang ada di keranjang.
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');

            // Kolom untuk menyimpan jumlah produk yang dipesan.
            // Kita gunakan unsignedInteger karena jumlah tidak mungkin negatif.
            $table->unsignedInteger('quantity');

            $table->timestamps(); // Ini akan membuat kolom `created_at` dan `updated_at` secara otomatis.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
