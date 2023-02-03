<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class StaffController extends Controller
{
    public function addStaff(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'firstname' => 'string|required',
            'lastname' => 'string|required',
            'phone' => 'string|required',
            'email' => 'email|required',
            'role' => 'integer|required'
        ]);
        if ($validatedData->fails()) {
            return response(['errors' => $validatedData->errors()->all()], 422);
        }
        $check = User::where(['business_id' => $this->bid()])->orWhere([
            'email' => $request->email,
            'phone' => $request->phone
        ])->count();

        if($check > 0) {
            return response([
                'message' => 'The Email address or Phone number has been asiggned to another staff'
            ], 409);
        }

        User::create([
            'business_id' => $this->bid(),
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
            'note' => $request->note,
            'role' => $request->role,
            'date_of_birth' => $request->date_of_birth,
            'password' => Hash::make($request->phone),
            'appointment_date' => $request->appointment_date,
            'appointment_type' => $request->appointment_type
        ]);
        return response([
            'message' => 'Staff has been added to business!'
        ], 200);  
    }
}
