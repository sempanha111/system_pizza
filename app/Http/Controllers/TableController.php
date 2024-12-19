<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TableController extends Controller
{
    public function addtable(Request $request)
    {

        $validated = $request->validate([
            'name' => 'required|string|min:3',
            'number' => 'required|string|min:3',
        ]);

        try {

            $table = Table::create([
                'name' => $validated['name'],
                'number' =>  $validated['number'],
            ]);

            return response()->json([
                'message' => 'Table Added Successfully!',
                'data' => $table,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong, please try again later.',
            ], 500);
        }
    }

    public function get_table()
    {
        $table = Table::all();
        return response()->json($table);
    }

    public function updatetable(Request $request, $id){
        $validated = $request->validate([
            'name' => 'required|string|min:3',
            'number' => 'required|string|min:3',
        ]);
        try{
            $table = Table::findOrFail($id);
            $table->update($validated);

            return response()->json([
                'message' => "Table Updated Successfully!",
                'data' => $table,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong, please try again later.',
            ], 500);
        }
    }

    public function deletetable($id)
    {
        try {
            $table = Table::findOrFail($id);

            $table->delete();
            return response()->json(['message' => 'Table deleted successfully!'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error deleting Table.'], 500);
        }
    }
}
