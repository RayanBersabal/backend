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
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('role')->default(json_encode([])); // Menggunakan JSON untuk array role
            $table->json('task')->default(json_encode([]));  // Menggunakan JSON untuk array task
            $table->string('image')->nullable(); // URL gambar avatar
            $table->string('github')->nullable(); // URL profil GitHub
            $table->timestamps(); // created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
