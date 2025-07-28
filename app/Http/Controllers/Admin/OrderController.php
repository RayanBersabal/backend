<?php

namespace App\Http\Controllers\Admin; // Perhatikan namespace ini harus App\Http\Controllers\Admin

use App\Http\Controllers\Controller; // Jangan lupa import Controller dasar
use App\Models\Order; // Import model Order Anda
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; // Opsional: untuk logging jika diperlukan

class OrderController extends Controller
{
    /**
     * Mengambil daftar SEMUA pesanan untuk panel admin.
     * Endpoint: GET /api/admin/orders
     */
    public function index()
    {
        // Ambil semua pesanan dari database.
        // Eager load relasi 'user' (jika ada) dan 'orderItems.product'
        // untuk mendapatkan data lengkap yang dibutuhkan frontend.
        // Urutkan berdasarkan created_at terbaru.
        $orders = Order::with(['user', 'orderItems.product'])
                        ->latest()
                        ->get();

        // Sesuaikan format data agar cocok dengan yang diharapkan frontend Vue.js Anda.
        return response()->json([
            'status' => 'success',
            'message' => 'All orders fetched successfully for admin.',
            'data' => $orders->map(function($order) {
                return [
                    'id' => $order->id,
                    'total' => $order->total_amount, // Sesuaikan dengan nama kolom total di tabel orders Anda
                    'status' => $order->status,
                    'createdAt' => $order->created_at,
                    'form' => [
                        'namaLengkap' => $order->customer_name,
                        'nomorHp' => $order->customer_phone,
                        'alamatPengantaran' => $order->delivery_address,
                        'pesanUntukDapur' => $order->notes,
                    ],
                    'ongkir' => $order->delivery_fee,
                    'biayaAdmin' => $order->admin_fee,
                    'items' => $order->orderItems->map(function($item) {
                        return [
                            'name' => $item->product_name ?? ($item->product ? $item->product->name : 'Unknown Product'),
                            'quantity' => $item->quantity,
                            'price' => $item->price,
                            // 'image_url' => $item->product ? $item->product->image_url : null, // Opsional jika ada
                        ];
                    }),
                ];
            }),
        ]);
    }

    /**
     * Memperbarui status pesanan tertentu.
     * Endpoint: PATCH /api/admin/orders/{order}/status
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order (Laravel akan otomatis menemukan order berdasarkan ID dari rute)
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(Request $request, Order $order)
    {
        // Validasi input status
        $request->validate([
            'status' => ['required', 'string', 'in:Dipesan,Disiapkan,Dikirim,Selesai,Dibatalkan'],
        ]);

        try {
            $order->status = $request->status;
            $order->save();

            // Opsional: Log perubahan status
            Log::info("Order {$order->id} status updated to {$order->status} by admin (User ID: " . ($request->user()->id ?? 'N/A') . ")");

            // Eager load relasi untuk respons yang lengkap kembali ke frontend
            $order->load(['user', 'orderItems.product']);

            return response()->json([
                'status' => 'success',
                'message' => 'Order status updated successfully.',
                'data' => [
                    'id' => $order->id,
                    'total' => $order->total_amount,
                    'status' => $order->status,
                    'createdAt' => $order->created_at,
                    'form' => [
                        'namaLengkap' => $order->customer_name,
                        'nomorHp' => $order->customer_phone,
                        'alamatPengantaran' => $order->delivery_address,
                        'pesanUntukDapur' => $order->notes,
                    ],
                    'ongkir' => $order->delivery_fee,
                    'biayaAdmin' => $order->admin_fee,
                    'items' => $order->orderItems->map(function($item) {
                        return [
                            'name' => $item->product_name ?? ($item->product ? $item->product->name : 'Unknown Product'),
                            'quantity' => $item->quantity,
                            'price' => $item->price,
                            // 'image_url' => $item->product ? $item->product->image_url : null,
                        ];
                    }),
                ]
            ]);
        } catch (\Exception $e) {
            // Tangani error jika terjadi
            Log::error("Failed to update order status for order {$order->id}: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update order status.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Anda bisa menambahkan metode lain di sini jika diperlukan,
    // misalnya public function show(Order $order) untuk melihat detail satu order admin.
}
