<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    // Register a new user
    public function register(Request $request)
    {
        $request->validate([
            'name'=> 'required|string|max:255',
            'gender'=> 'required|string|in:male,female',
'email'=> 'required|string|email|max:255|unique:users',
'password'=> 'required|string|min:8|confirmed'

        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'gender' => $request->gender
        ]);

        //create token
        $token = $user->createToken('auth_token')->plainTextToken;


        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
            'token' => $token
        ], 201);
    }

    public function deleteUser(Request $request)
    {
        $user = $request->user();
        $user->delete();
        return response()->json(['message' => 'User deleted successfully'], 200);
    }

    public function login(Request $request)
    {
       $request->validate([
'email'=> 'required|email|',
'password'=> 'required|string|min:8|confirmed'

        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
//that's in case that i heard in the meeting you wanna when the user regustered it redirect to profile so token will be generated in the register
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User logged in successfully',
            'user' => $user,
            'token' => $token
        ], 200);

    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'User logged out successfully'], 200);
    }

    //get all users
    public function getUsers()
    {
        $users = User::all();
        if ($users->isEmpty()) {
            return response()->json(['message' => 'No users found'], 201);
        }
        return response()->json($users);
    }
    //reset password
    public function resetPassword(Request $request)
    {
        $user = auth()->user();
        $user->password = Hash::make($request->password);
        $user->save();
        return response()->json(['message' => 'Password reset successfully'], 200);
    }




    
     
}
