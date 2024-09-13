<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email',
                'password' => 'required|string|min:8',
            ]);
            if ($validator->fails()) 
            {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $user=new User();
            $user->name=$request->name;
            $user->email=$request->email;
            $user->password=Hash::make($request->password);
            $user->save();
            $token = $user->createToken('authToken')->plainTextToken;
            if($user=true)
            {
                return response()->json(['status' => 'User registered successfully','token' => $token], 201);
            }
            else
            {
                return response()->json(['status'=>500, 'response'=>'something went wrong !!']);
            }
    }
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);
        if ($validator->fails()) 
        {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) 
        {
            $user = Auth::user();
            $token = $user->createToken('authToken')->plainTextToken;
            return response()->json(['msg'=>'You are logedin successfully !!','token' => $token], 200);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }
    public function logout(Request $request)
    {
        // Log the entire request headers to see if the Authorization header is present
        // \Log::info('Request headers:', $request->headers->all());

        // Log the bearer token specifically
        $token = $request->bearerToken();
        // \Log::info('Token before logout:', ['token' => $token]);

        if ($token) {
            // Proceed to delete the token if it's present
            $request->user()->currentAccessToken()->delete();
            return response()->json(['message' => 'Logged out successfully', 'status' => 200]);
        }

        return response()->json(['message' => 'Token not found or invalid', 'status' => 401]);
    }
}
