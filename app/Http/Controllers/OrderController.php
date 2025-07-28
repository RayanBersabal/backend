<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User; // Pastikan ini di-import
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Display a listing of the user's orders.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $orders = $user->orders()->with('orderItems.product')->latest()->get();

        return response()->json([
            'status' => 'success',
            'message' => 'User orders fetched successfully.',
            'data' => $orders->map(function($order) {
                return [
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
                        ];
                    }),
                ];
            }),
        ]);
    }

    /**
     * Display the specified order.
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        $order = $user->orders()->with('orderItems.product')->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'message' => 'Order details fetched successfully.',
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
                    ];
                }),
            ]
        ]);
    }

    /**
     * Store a newly created order in storage.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        $validatedData = $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'delivery_address' => 'required|string|max:500',
            'notes' => 'nullable|string|max:500',
            // === PERBAIKI BAGIAN INI ===
            'payment_type' => 'required|string|in:cod,qris_dummy,bank_transfer', // Sesuaikan dengan nilai dari frontend
            // ==========================
        ]);

        $subtotal = 0;
        $deliveryFee = 10000;
        $adminFee = 2000;
        $orderItemsData = [];

        foreach ($validatedData['items'] as $item) {
            $product = Product::findOrFail($item['product_id']);
            $itemPrice = $product->price;
            $itemSubtotal = $itemPrice * $item['quantity'];
            $subtotal += $itemSubtotal;

            $orderItemsData[] = new OrderItem([
                'product_id' => $product->id,
                'product_name' => $product->name,
                'price' => $itemPrice,
                'quantity' => $item['quantity'],
            ]);
        }

        $totalAmount = $subtotal + $deliveryFee + $adminFee;

        $order = $user->orders()->create([
            'customer_name' => $validatedData['customer_name'],
            'customer_phone' => $validatedData['customer_phone'],
            'delivery_address' => $validatedData['delivery_address'],
            'notes' => $validatedData['notes'],
            'payment_type' => $validatedData['payment_type'],
            'subtotal' => $subtotal,
            'delivery_fee' => $deliveryFee,
            'admin_fee' => $adminFee,
            'total_amount' => $totalAmount,
            'payment_status' => 'pending',
            'status' => 'Dipesan',
        ]);

        $order->orderItems()->saveMany($orderItemsData);
        $order->load('orderItems.product');

        return response()->json([
            'status' => 'success',
            'message' => 'Pesanan berhasil dibuat!',
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
                        'name' => $item->product_name,
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                    ];
                }),
            ]
        ], 201);
    }

    /**
     * Update the specified order status in storage.
     */
    public function updateOrderStatus(Request $request, $orderId)
    {
        // Implementasikan logika untuk update status pesanan
    }
}
