<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:8|max:255',
        ]); 

        $user = User::where('email',$request->email)->first();

        if(!$user || !Hash::check($request->password, $user->password)){
        return response()->json([
            'message' => 'the provided credentials are incorrect'
        ], 401);
    }

        $token = $user->createToken($user->name.'Auth-Token')->plainTextToken;
        return response()->json([
            'message' => 'Login Succesful',
            'token_type' => 'Bearer',
            'token' => $token
        ], 200);
    }

    // registration of new user
    public function register(Request $request) : JsonResponse 
    {
            $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|string|min:8|max:255',
        ]);

        
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        if($user) {
             $token = $user->createToken($user->name.'Auth-Token')->plainTextToken;
             return response()->json([
                    'message' => 'Registration Succesful',
                    'token_type' => 'Bearer',
                    'token' => $token
            ], 201);

        } 
        
        else {

            return response()->json([
                
                'message' => 'something went wrong! while registration'
            ],500);

        }
    }

    // logout

    public function logout(Request $request)
{
    $user = $request->user();

    if ($user) {
        $user->currentAccessToken()->delete();
        return response()->json([
            'message' => 'Logout Successfully',
        ], 200);
    } else {
        return response()->json([
            'message' => 'User not found',
        ], 404);
    }
}

    // checking for profile
    public function profile(Request $request){
        
        if($request->user()){

            return response()->json([
                
                'message' => 'Profile fetched',
                'data' => $request->user()
            ],200);

        } else
        
        {

             return response()->json([
                
                'message' => 'Profile not authenticated'
            ],401);
        }

    }

}
