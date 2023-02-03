<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'email' => 'email|required|exists:users,email',
            'password' => 'required',
        ]);
        if ($validatedData->fails()) {
            return response(['errors' => $validatedData->errors()->all()], 422);
        }
        if (!auth()->attempt(['email' => $request->email, 'password' => $request->password])) {
            return response(['message' => 'Invalid Credentials'], 401);
        }
        $accessToken = auth()->user()->createToken('authToken')->accessToken;
        $user = auth()->user(); $user = [ 
            'firstname' => $user->firstname, 'lastname' => $request->lastname, 'email' => $user->email, 'phone' => $user->phone,
            'role' => $user->role
        ];
        $business = auth()->user()->business; $business = [
            'name' => $business->name, 'phone' => $business->phone, 'email' => $business->email, 'address' => $business->address
        ];
        return response([
            'message' => 'Login successfull', 'access_token' => $accessToken, 'data' => ['user' => $user, 'business' => $business],  
        ], 200);
    }
}
