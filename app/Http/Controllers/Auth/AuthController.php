<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validation = Validator::make($request->all(), [
            "name" => "required|string|min:3",
            "email" => "required|email",
            "password" => "required|min:8|confirmed"
        ]);

        if($validation->fails()){
            return response()->json([
                "message" => "Invalid Field",
                "errors" => $validation->errors()
            ], 422);
        }

        $user = User::create($request->all());
        $data = $user;

        $data["token"] = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            "message" => "Register success",
            "data" => $data
        ], 200);

    }

    public function login(Request $request)
    {
        $validation = Validator::make($request->all(), [
            "email" => "required|email",
            "password" => "required|min:8"
        ]);

        if($validation->fails()){
            return response()->json([
                "message" => "Invalid Field",
                "errors" => $validation->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if(!$user || !Hash::check($request->password, $user->password)){
            return response()->json([
                "message" => "Email or password incorrect"
            ], 401);
        }

        $data = $user;
        $data["accessToken"] = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            "message" => "Login success",
            "user" => $data
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            "message" => "Logout success"
        ], 200);
    }
}
