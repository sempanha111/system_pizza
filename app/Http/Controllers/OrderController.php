<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Orderitem;
use App\Models\Table;
use Carbon\Carbon;
use Carbon\Exceptions\Exception;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function addorder(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'totalPrice' => 'required|numeric',
            'orderType' => 'required|string',
            'tables_id' => 'nullable|exists:tables,id',
            'customerName' => 'required|string',
            'contactNumber' => 'required|string',
            'users_id' => 'required|exists:users,id',
            'isDiscount' => 'required|boolean',
            'paymentMethod' => 'required|string',
            'status' => 'required|boolean'
        ]);

        try {
            // Create the Order
            $order = Order::create([
                'users_id' => $request->users_id,
                'tables_id' => $request->tables_id ?? null,
                'status' => $request->status,
                'customerName' => $request->customerName,
                'contactNumber' => $request->contactNumber,
                'paymentMethod' => $request->paymentMethod,
                'orderType' => $request->orderType,
                'isDiscount' => $request->isDiscount,
            ]);

            // Add Order Items
            foreach ($request->items as $item) {
                Orderitem::create([
                    'orders_id' => $order->id,
                    'products_id' => $item['id'],
                    'quantity' => $item['quantity'],
                ]);
            }

            return response()->json([
                'message' => "Order Added Successfully!",
                'data' => $validated
            ], 201);
        } catch (\Exception $e) {
            // Catch any unexpected errors and return a generic error message
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function get_order()
    {
        $orders = Order::whereDate('created_at', Carbon::today())->get();


        return response()->json([
            'message' => 'Today\'s orders retrieved successfully',
            'data' => $orders,
        ]);
    }

    public function changestatusorder($id)
    {
        try {
            $order = Order::findOrFail($id);
            if ($order) {
                $order->status = false;
                $order->save();
            }
            return response()->json([
                'message' => "Order Added Successfully!"
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function changeorderfield(Request $request, $id)
    {
        try {
            $order = Order::findOrFail($id);

            // Update only the fields provided in the request
            $order->update($request->only(['status_accept', 'status_cook', 'status_ready']));

            return response()->json([
                'message' => 'Order status updated successfully',
                'data' => $order,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function get_order_item($id)
    {
        try {
            $order = Order::findOrFail($id);
            $items = $order->orderItems()->with('product')->get()->map(function ($item) {
                return [
                    'product_id' => $item->id, // Only get the product_id
                    'product_name' => $item->product ? $item->product->name : null, // Optional: Access related product data
                    'price' => $item->product ? $item->product->price : null,
                    'description' => $item->product ? $item->product->description : null,
                    'image' => $item->product ? $item->product->image : null,
                    'quantity' => $item->quantity, // Example of other relevant data in orderItems
                    // Add any other fields you want to include here
                ];
            });
            $data = [
                'order' => $order,
                'items' => $items
            ];
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    public function get_history(Request $request)
    {
        try {
            
            $query = Order::query();

            // Apply filters based on request parameters
            if ($request->filled('from_date')) {
                $query->whereDate('created_at', '>=', $request->from_date);
            }

            if ($request->filled('to_date')) {
                $query->whereDate('created_at', '<=', $request->to_date);
            }

            // Default behavior: Fetch today's orders if no filters are provided
            if (!$request->filled('from_date') && !$request->filled('to_date')) {
                $query->whereDate('created_at', Carbon::today());
            }

            // Fetch filtered orders along with related items
            $orders = $query->with(['orderItems.product'])->get();

            // Format the data as needed
            $data = $orders->map(function ($order) {
                return [
                    'order' => $order,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }



}
