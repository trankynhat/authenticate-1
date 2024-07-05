<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Admin;
use App\Models\Guest;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Register new guest user
    public function registerGuest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|',
            'contact_phone' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toArray(), 422);
        }

        // Tạo user mới
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Tạo guest tương ứng
        Guest::create([
            'user_id' => $user->id,
            'contact_phone' => $request->contact_phone,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Guest registered successfully'], 201);
    }


    // Register new organization user
    public function registerOrganization(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|',
            'company_name' => 'required|string|max:255',
            'company_address' => 'required|string|max:255',
            'contact' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toArray(), 422);
        }

        // Tạo user mới
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Tạo organization tương ứng
        Organization::create([
            'user_id' => $user->id,
            'company_name' => $request->company_name,
            'company_address' => $request->company_address,
            'contact' => $request->contact,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Organization registered successfully'], 201);
    }

    // User login
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toArray(), 422);
        }

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Check the role of the user
            if ($user->role === 'guest') {
                $guestInfo = Guest::where('user_id', $user->id)->first();
                $userData = [
                    'user' => $user,
                    'guest_info' => $guestInfo
                ];
            } elseif ($user->role === 'organization') {
                $organizationInfo = Organization::where('user_id', $user->id)->first();
                $userData = [
                    'user' => $user,
                    'organization_info' => $organizationInfo
                ];
            } else {
                $userData = $user;
            }

            return response()->json($userData, 200);
        } else {
            return response()->json(['message' => 'The provided credentials are incorrect.'], 401);
        }
    }
    public function getAllUsers(Request $request)
    {
        // Validate the input
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Authenticate the user
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Check if the user is an admin
            $isAdmin = Admin::where('user_id', $user->id)->exists();

            if (!$isAdmin) {
                return response()->json(['message' => 'Unauthorized'], 403); // Return an error if not an admin
            }

            // Get the list of users with the role of guest and organization
            $guests = Guest::with('user')->get(); // Get guest information and related user information
            $organizations = Organization::with('user')->get(); // Get organization information and related user information

            $users = [
                'guests' => $guests,
                'organizations' => $organizations
            ];

            return response()->json($users, 200);
        } else {
            return response()->json(['message' => 'The provided credentials are incorrect.'], 401);
        }
    }

}
