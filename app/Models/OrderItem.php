<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_name', // Penting: Pastikan ini ada
        'price',
        'quantity',
    ];

    // Relasi ke Order induk
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Relasi ke Product asli (optional, tapi bagus untuk melihat detail produk saat ini)
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
