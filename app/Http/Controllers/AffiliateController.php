<?php

namespace App\Http\Controllers;

use App\Models\Affiliate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AffiliateController extends Controller
{
    public function getAffiliates()
    {
        return Affiliate::where('business_id', $this->bid() )->orderby('updated_at', 'desc')->paginate(100);
    }

    public function deleteAffiliate($id)
    {
        return response([
            'message' => 'Affiliate has been deleted sucessfuly'
        ]);
    }


    public function updateAffiliate(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'affiliate_id' => 'required|exists:affiliates,id',
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
        Affiliate::where('id', $request->affiliate_id)->update([
            'fullname' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'category' => $request->category,
            'address' => $request->address,
            'note' => $request->note
        ]);
        return response([
            'message' => 'Affiliate profile('.$request->name.') has been updated'
        ], 200);
    }


    public function registerAffiliate(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'name' => 'string|required',
            'phone' => 'string|required',
            'email' => 'email|required',
        ]);
    
        if ($validatedData->fails()) {
            return response(['errors' => $validatedData->errors()->all()], 422);
        }
        $check = Affiliate::where(['business_id' => $this->bid()])->WHERE('email', $request->email)->orWHERE('phone', $request->phone)->count();
        if($check > 0) {
            return response([
                'message' => 'The Email address or Phone number has been asiggned to another affiliate'
            ], 409);
        }

        Affiliate::create([
            'business_id' => $this->bid(),
            'fullname' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
            'category' => $request->category,
            'note' => $request->note
        ]);
        return response([
            'message' => 'Affiliate ('.$request->name.') has been added to business!'
        ], 200);  
    }
}
