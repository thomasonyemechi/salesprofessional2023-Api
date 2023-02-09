<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class StaffController extends Controller
{
    function getAllStaff()
    {
        return User::where(['business_id' => $this->bid(), ['role', '!=', '1'] ])->paginate(25, [
            'id','firstname', 'lastname', 'email', 'phone', 'address', 'role', 'appointment_date', 'appointment_type', 'date_of_birth', 'note'
        ]);
    }

    public function updateStaff(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'staff_id' => 'required|exists:users,id',
            'firstname' => 'string|required',
            'lastname' => 'string|required',
            'role' => 'integer|required'
        ]);
        if ($validatedData->fails()) {
            return response(['errors' => $validatedData->errors()->all()], 422);
        }

        User::where('id', $request->staff_id)->update([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'address' => $request->address,
            'note' => $request->note,
            'role' => $request->role,
            'date_of_birth' => $request->date_of_birth,
            'appointment_date' => $request->appointment_date,
            'appointment_type' => $request->appointment_type
        ]);
        return response([
            'message' => 'Staff profile ('.$request->firstname.') has been updated'
        ], 200);  
    }



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
        $check = User::where(['business_id' => $this->bid()])->WHERE('email', $request->email)->orWHERE('phone', $request->phone)->count();
        if($check > 0) {
            return response([
                'message' => 'The Email address or Phone number has been asiggned to another staff'
            ], 409);
        }

        $staff = User::create([
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
            'message' => 'Staff ('.$request->firstname.') has been added to business!',
            'id' => $staff->id
        ], 200);  
    }
}
