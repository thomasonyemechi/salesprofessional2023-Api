<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    public function getCustomers()
    {
        return Customer::where('business_id', $this->bid() )->orderby('updated_at', 'asc')->paginate(100);
    }

    public function deleteAffiliate($id)
    {
        return response([
            'message' => 'Affiliate has been deleted sucessfully'
        ]);
    }

    public function updateCustomer(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'customer_id' => 'required|exists:affiliates,id',
            'name' => 'string|required',
            'phone' => 'string|required',
            'email' => 'email|required',
        ]);
        if ($validatedData->fails()) {
            return response(['errors' => $validatedData->errors()->all()], 422);
        }
        Customer::where('id', $request->customer_id)->update([
            'fullname' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'category' => $request->category,
            'address' => $request->address,
            'note' => $request->note,
            'referral_id' => $request->referral_id ?? 0
        ]);
        return response([
            'message' => 'Customer profile('.$request->name.') has been updated'
        ], 200);
    }



    public function addCustomer(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'name' => 'string|required',
            'phone' => 'string|required',
            'email' => 'email|required',
        ]);
        if ($validatedData->fails()) {
            return response(['errors' => $validatedData->errors()->all()], 422);
        }
        $check = Customer::where(['business_id' => $this->bid()])->WHERE('email', $request->email)->orWHERE('phone', $request->phone)->count();
        if($check > 0) {
            return response([
                'message' => 'The Email address or Phone number has been asiggned to another customer'
            ], 409);
        }

        Customer::create([
            'business_id' => $this->bid(),
            'fullname' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
            'category' => $request->category,
            'note' => $request->note,
            'referral_id' => $request->referral_id ?? 0
        ]);
        return response([
            'message' => 'Customer ('.$request->name.') has been added to business!'
        ], 200);  
    }
}
