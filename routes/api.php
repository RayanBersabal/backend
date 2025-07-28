<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ReviewController;

// Public Auth Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/admin-login', [AuthController::class, 'adminLogin']);

// Public Product Access (GET only)
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::get('/products/{product}/reviews', [ReviewController::class, 'index']);
// Digunakan oleh halaman About.vue untuk menampilkan daftar tim
Route::get('/members', [MemberController::class, 'index']);


// Routes untuk User
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Cart Routes
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart', [CartController::class, 'store']);
    Route::patch('/cart/{cart}', [CartController::class, 'updateQuantity']);
    Route::delete('/cart/{cart}', [CartController::class, 'destroy']);
    Route::post('/cart/clear', [CartController::class, 'clearCart']);

    // User Order Routes
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::post('/orders', [OrderController::class, 'store']);

    Route::post('/products/{product}/reviews', [ReviewController::class, 'store']);
    Route::put('/reviews/{review}', [ReviewController::class, 'update']);
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy']);
});

// Admin-only Routes
Route::middleware(['auth:sanctum','is_admin'])->prefix('admin')->group(function () {
    // Admin-only Product Management
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);

    // Admin-only Order Management
    Route::get('/orders', [AdminOrderController::class, 'index']);
    Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus']);
    // Route::get('/orders/{order}', [AdminOrderController::class, 'show']); // Opsional

    // --- Rute Manajemen Anggota Tim (Hanya untuk Admin) ---
    // Ini akan mendaftarkan rute: POST /admin/members, PUT/PATCH /admin/members/{id}, DELETE /admin/members/{id}
    // Serta GET /admin/members (untuk daftar) dan GET /admin/members/{id} (untuk detail)
    Route::apiResource('members', MemberController::class);
});



// Health check untuk memastikan api ada / terhubung (opsional)
Route::get('/', fn () => response()->json(['message' => 'Api Santapin']));
Route::get('/ping', fn () => response()->json(['message' => 'pong-pong']));
