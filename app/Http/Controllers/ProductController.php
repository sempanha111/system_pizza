<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function addproduct(Request $request)
    {
        $validatedData = $request->validate([
            'categories_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB
        ]);

        try {
            // Handle image upload
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('products', 'public');
                $validatedData['image'] = $imagePath;
            }

            $product = Product::create($validatedData);

            return response()->json([
                'message' => 'Product added successfully',
                'data' => $product
            ], 201);
        } catch (\Exception $e) {
            // Catch any unexpected errors and return a generic error message
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong, please try again later.',
            ], 500);
        }
    }

    public function get_product()
    {
        $products = Product::all();
        return response()->json($products);
    }

    public function updateproduct(Request $request, $id)
    {
        $validatedData = $request->validate([
            'categories_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {

            $product = Product::findOrFail($id);
            $product->name = $validatedData['name'];
            $product->description = $validatedData['description'];
            $product->price = $validatedData['price'];

            if ($request->hasFile('image')) {
                // Delete the old image if it exists
                if ($product->image && Storage::disk('public')->exists($product->image)) {
                    Storage::disk('public')->delete($product->image);
                }

                // Store the new image
                $path = $request->file('image')->store('products', 'public');
                $product->image = $path;
            }

            $product->save();

            return response()->json([
                'message' => 'Product updated successfully',
                'data' => $product,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong, please try again later.',
            ], 500);
        }
    }

    public function deleteproduct($id)
    {
        try {
            $product = Product::findOrFail($id);
            // Delete the image file if it exists
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $product->delete();
            return response()->json(['message' => 'Product deleted successfully!'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error deleting Product.'], 500);
        }
    }
}
