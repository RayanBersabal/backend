<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
    ];

    /**
     * Define the relationship with the Product model.
     * A Cart item belongs to one Product.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Define the relationship with the User model.
     * A Cart item belongs to one User. (Optional, but good for completeness)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
