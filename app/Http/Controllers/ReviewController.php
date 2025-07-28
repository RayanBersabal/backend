<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ReviewController extends Controller
{
    public function index(Product $product)
    {
        return response()->json($product->reviews()->with('user')->latest()->get());
    }


    public function store(Request $request, Product $product)
    {
        try {
            $validatedData = $request->validate([
                'rating' => 'required|integer|min:1|max:5',
                'comment' => 'nullable|string|max:1000',
            ]);

            if ($product->reviews()->where('user_id', $request->user()->id)->exists()) {
                return response()->json([
                    'message' => 'Anda sudah memberikan review untuk produk ini.'
                ], 409);
            }

            $review = $product->reviews()->create([
                'user_id' => $request->user()->id,
                'rating' => $validatedData['rating'],
                'comment' => $validatedData['comment'] ?? null,
            ]);

            // Muat user relationship untuk response
            $review->load('user');

            return response()->json([
                'message' => 'Review berhasil dikirim!',
                'review' => $review
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat mengirim review.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Review $review)
    {
        // Authorization: Hanya pemilik review atau admin yang bisa update
        if ($request->user()->id !== $review->user_id && !$request->user()->is_admin) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        try {
            $validatedData = $request->validate([
                'rating' => 'required|integer|min:1|max:5',
                'comment' => 'nullable|string|max:1000',
            ]);

            $review->update($validatedData);
            $review->load('user');

            return response()->json([
                'message' => 'Review berhasil diperbarui!',
                'review' => $review
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat memperbarui review.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Request $request, Review $review)
    {
        // Authorization: Hanya pemilik review atau admin yang bisa menghapus
        if ($request->user()->id !== $review->user_id && !$request->user()->is_admin) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        try {
            $review->delete();
            return response()->json(['message' => 'Review berhasil dihapus!'], 204);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat menghapus review.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
