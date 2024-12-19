<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function adduser(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:6',
                'role_id' => 'required|string',
            ]);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role_id' => $validated['role_id'],
            ]);

            // Return user data along with the role name
            return response()->json([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role_id' => $user->role_id,
                'role_name' => $user->role->name ?? null, // Include role name
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(), // Return validation errors
            ], 422); // 422 Unprocessable Entity for validation errors
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong, please try again later.',
            ], 500);
        }
    }

    public function fetchuser()
    {
        $users = User::all();
        $users = $users->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role_id' => $user->role_id,
                'role_name' => $user->role->name ?? null, // Include role name
            ];
        });
        return response()->json($users);
    }

    public function updateuser(Request $request, $id)
    {

        // Validate incoming data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:6', // Password is optional
            'role_id' => 'required|exists:roles,id',
        ]);

        try {
            // Find the user by ID
            $user = User::findOrFail($id);

            // Update user details
            $user->name = $validatedData['name'];
            $user->email = $validatedData['email'];

            if (!empty($validatedData['password'])) {
                $user->password = Hash::make($validatedData['password']);
            }

            $user->role_id = $validatedData['role_id'];

            $user->save(); // Save changes

            // return response()->json($user, 200); // Return updated user data

            return response()->json([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role_id' => $user->role_id,
                'role_name' => $user->role->name ?? null, // Include role name
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(), // Return validation errors
            ], 422); // 422 Unprocessable Entity for validation errors
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong, please try again later.',
            ], 500);
        }
    }

    public function deleteuser($id)
    {
        try {
            $user = User::findOrFail($id); // Find the user or throw 404
            $user->delete(); // Delete the user

            return response()->json(['message' => 'User deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete user: ' . $e->getMessage()], 500);
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        try {
            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'message' => 'Invalid Credentals'
                ]);
            }

            $token = Str::random(80);
            $user->update([
                'api_token' => $token
            ]);

            return response()->json([
                'token' => $token
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(), // Return validation errors
            ]); // 422 Unprocessable Entity for validation errors
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong, please try again later.',
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $token = $request->bearerToken(); // Get the token from the request header

            $user = User::where('api_token', $token)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User Not found',
                ]); // Return unauthorized if no user is authenticated
            }

            $user->update(['api_token' => null]); // Logout user
            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully.',
            ]);

        } catch (\Exception $e) {
            Log::error('Logout error: ' . $e->getMessage()); // Log the exception
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

}
