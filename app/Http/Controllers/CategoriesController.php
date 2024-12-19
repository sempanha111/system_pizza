<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Categories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;

class CategoriesController extends Controller
{
    public function addcategories(Request $request)
    {
        // Validation for the incoming request
        $validated = $request->validate([
            'name' => 'required|string|max:255', // You might want to add string and max length validation
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Validate image types and max size
        ]);

        try {
            // Store the image and get the file path
            $path = $request->file('image')->store('images', 'public'); // Store image in the 'public' disk


            $category = Categories::create([
                'name' => $validated['name'],
                'image' => $path,
            ]);


            // Return a successful response with image URL
            return response()->json([
                'message' => 'Category added successfully!',
                'data' => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'image' => $category->image,
                ],
            ], 200);
        } catch (\Exception $e) {
            // Catch any unexpected errors and return a generic error message
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong, please try again later.',
            ], 500);
        }
    }

    public function get_categories(Request $request)
    {
        $categorys = Categories::all();
        return response()->json($categorys);
    }



    public function updatecategories(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            $category = Categories::findOrFail($id);
            $category->name = $validated['name'];

            if ($request->hasFile('image')) {
                // Delete the old image if it exists
                if ($category->image && Storage::disk('public')->exists($category->image)) {
                    Storage::disk('public')->delete($category->image);
                }

                // Store the new image
                $path = $request->file('image')->store('images', 'public');
                $category->image = $path;
            }

            $category->save();

            return response()->json([
                'message' => 'Category updated successfully!',
                'data' => $category,
            ]);
        } catch (\Exception $e) {
            // Catch any unexpected errors and return a generic error message
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong, please try again later.',
            ], 500);
        }
    }

    public function deletecategories($id){
        try {
            $category = Categories::findOrFail($id);

            // Delete the image file if it exists
            if ($category->image && Storage::disk('public')->exists($category->image)) {
                Storage::disk('public')->delete($category->image);
            }

            $category->delete();

            return response()->json(['message' => 'Category deleted successfully!'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error deleting category.'], 500);
        }
    }
}
