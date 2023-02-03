<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class BusinessController extends Controller
{
    public function createBusiness(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'email' => 'email|required|unique:businesses,email',
            'password' => 'string|required',
            'phone' => 'string|required|unique:businesses,phone',
            'name' => 'string|required|min:3',
        ]);
        if ($validatedData->fails()) {
            return response(['errors' => $validatedData->errors()->all()], 422);
        }
        $business = Business::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email
        ]);
        User::create([
            'business_id' => $business->id,
            'email' => $request->email,
            'role' => 1,
            'firstname' => $request->name,
            'lastname' => '',
            'phone' => $request->phone,
            'password' => Hash::make($request->password)
        ]);

        return response([
            'message' => 'Buisness ('.$request->name.') has been Created',
        ], 200);
    }


    function businessUsers($business_id, $opt='all_users')
    {
        if($opt == 'all_users') {
            return User::where('business_id', $business_id)->paginate(['id','name','email','phone','address'], 100);
        }
    }

}
