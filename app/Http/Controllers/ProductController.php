<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    // GET /api/products
    public function index(Request $request)
    {
        $query = Product::query();
        if ($request->has('category') && in_array($request->category, ['Makanan', 'Minuman'])) {
            $query->where('category', $request->category);
        }

        return response()->json([
            'data' => $query->orderBy('created_at', 'desc')->get()
        ]);
    }

    // GET /api/products/{id}
    public function show($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Produk tidak ditemukan'], 404);
        }

        return response()->json($product);
    }

    // POST /api/products (or /api/admin/products based on your route)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'required|string',
            'price'       => 'required|numeric|min:0', // Added min:0
            'category'    => ['required', Rule::in(['Makanan', 'Minuman'])],
            'image'       => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        } else {
            // If no image file is provided, ensure 'image' key is unset or set to null
            // This is important if your product model has a default image or allows null.
            $validated['image'] = null;
        }

        $product = Product::create($validated);

        return response()->json($product, 201);
    }

    // PUT /api/products/{id} (or /api/admin/products/{id} based on your route)
    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Produk tidak ditemukan'], 404);
        }

        $validated = $request->validate([
            'name'        => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'price'       => 'sometimes|required|numeric|min:0', // Added min:0
            'category'    => ['sometimes', 'required', Rule::in(['Makanan', 'Minuman'])],
            'image'       => 'nullable|image|max:2048', // Allow image to be null for updates
        ]);

        // Handle image update/deletion
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        } elseif (array_key_exists('image', $request->all()) && $request->input('image') === null) {
            // This case handles explicit removal of image from frontend (if input type="file" sent null)
            // Or if you have a "clear image" checkbox in frontend that sends null.
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = null; // Set image path to null in DB
        } else {
            // If image was not sent in request, do not modify the existing image path in DB.
            // Remove 'image' from $validated array so it's not updated to null mistakenly.
            unset($validated['image']);
        }


        $product->update($validated);

        return response()->json($product);
    }

    // DELETE /api/products/{id}
    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Produk tidak ditemukan'], 404);
        }

        // Delete image if exists
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return response()->json(['message' => 'Produk berhasil dihapus']);
    }
}
