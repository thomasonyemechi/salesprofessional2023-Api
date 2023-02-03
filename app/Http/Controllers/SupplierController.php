<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SupplierController extends Controller
{
    public function getSuppliers()
    {
        return Supplier::where('business_id', $this->bid() )->orderby('updated_at', 'asc')->paginate(100);
    }

    public function deleteSupplier($id)
    {
        return response([
            'message' => 'Supplier Has been deleted Sucessfully'
        ]);
    }

    public function updateSupplier(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'supplier_id' => 'required|exists:suppliers,id',
            'name' => 'string|required',
            'phone' => 'string|required',
            'email' => 'email|required',
            'category' => '',
            'address' => '',
            'note' => ''
        ]);
        if ($validatedData->fails()) {
            return response(['errors' => $validatedData->errors()->all()], 422);
        }
        Supplier::where('id', $request->supplier_id)->update([
            'fullname' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'category' => $request->category,
            'address' => $request->address,
            'note' => $request->note
        ]);
        return response([
            'message' => 'Supplier profile('.$request->name.') has been updated'
        ], 200);
    }


    public function addSupplier(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'name' => 'string|required',
            'phone' => 'string|required',
            'email' => 'email|required',
        ]);
        if ($validatedData->fails()) {
            return response(['errors' => $validatedData->errors()->all()], 422);
        }
        $check = Supplier::where(['business_id' => $this->bid()])->WHERE('email', $request->email)->orWHERE('phone', $request->phone)->count();
        if($check > 0) {
            return response([
                'message' => 'The Email address or Phone number has been asiggned to another supplier'
            ], 409);
        }

        Supplier::create([
            'business_id' => $this->bid(),
            'fullname' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
            'category' => $request->category,
            'note' => $request->note
        ]);
        return response([
            'message' => 'Supplier has been added to business!'
        ], 200);  
    }
}
