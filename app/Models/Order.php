<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'customer_name',
        'customer_phone',
        'delivery_address',
        'notes',
        'payment_type',
        'subtotal',
        'delivery_fee',
        'admin_fee',
        'total_amount',
        'payment_status',    // Kolom baru
        'payment_reference', // Kolom baru
        'paid_at',           // Kolom baru
        'status',            // Kolom status pesanan yang sudah ada
        'estimated_delivery_time',
    ];

    // Relasi ke User yang memesan
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke OrderItems (item-item dalam pesanan ini)
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // ... metode atau relasi lain jika ada
}
