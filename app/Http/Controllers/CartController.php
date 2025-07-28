<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product; // Tambahkan ini jika belum ada
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Menampilkan semua item di keranjang milik user yang sedang login.
     * Ini akan dipanggil oleh fetchCart() di Vue.
     */
    public function index()
    {
        // Ambil semua item cart milik user, beserta data produknya (relasi)
        // Pastikan relasi 'product' ada di model Cart dan menunjuk ke model Product.
        $cartItems = Cart::with('product')
                         ->where('user_id', Auth::id())
                         ->get();

        return response()->json($cartItems);
    }

    /**
     * Menambahkan item baru ke keranjang atau mengupdate quantity jika sudah ada.
     * Ini akan dipanggil oleh addToCart() di Vue.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1', // Pastikan quantity minimal 1 untuk penambahan awal
        ]);

        $user = Auth::user();
        $productId = $request->product_id;
        $quantity = $request->quantity;

        // Cek apakah produk yang sama sudah ada di keranjang user
        $cartItem = Cart::where('user_id', $user->id)
                         ->where('product_id', $productId)
                         ->first();

        if ($cartItem) {
            // Jika sudah ada, tambahkan quantity-nya
            $cartItem->quantity += $quantity;
            $cartItem->save();
        } else {
            // Jika belum ada, buat entri baru
            $cartItem = Cart::create([
                'user_id' => $user->id,
                'product_id' => $productId,
                'quantity' => $quantity,
            ]);
        }

        // Muat relasi produk untuk dikirim kembali sebagai respons
        $cartItem->load('product');

        return response()->json([
            'message' => 'Product added to cart successfully!',
            'cartItem' => $cartItem
        ], 201);
    }

    /**
     * Mengupdate kuantitas item tertentu di keranjang.
     * Dipanggil untuk increment atau decrement.
     */
    public function updateQuantity(Request $request, Cart $cart)
    {
        // Pastikan user hanya bisa mengupdate item dari keranjangnya sendiri (Authorization)
        if ($cart->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'quantity' => 'required|integer|min:0', // Kuantitas bisa 0 untuk dihapus
        ]);

        $newQuantity = $request->quantity;

        if ($newQuantity == 0) {
            // Jika kuantitas menjadi 0, hapus item dari keranjang
            $cart->delete();
            return response()->json(['message' => 'Item removed from cart successfully']);
        } else {
            // Update kuantitas
            $cart->quantity = $newQuantity;
            $cart->save();
            // Muat relasi produk untuk dikirim kembali sebagai respons
            $cart->load('product');
            return response()->json([
                'message' => 'Cart item quantity updated successfully!',
                'cartItem' => $cart
            ]);
        }
    }


    /**
     * Menghapus satu item dari keranjang.
     * Ini akan dipanggil oleh removeFromCart() di Vue.
     */
    public function destroy(Cart $cart)
    {
        // Pastikan user hanya bisa menghapus item dari keranjangnya sendiri (Authorization)
        if ($cart->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $cart->delete();

        return response()->json(['message' => 'Item removed from cart successfully']);
    }

    /**
     * Mengosongkan seluruh keranjang user.
     * Ini akan dipanggil setelah user checkout.
     */
    public function clearCart()
    {
        Cart::where('user_id', Auth::id())->delete();
        return response()->json(['message' => 'Cart cleared successfully!']);
    }
}
