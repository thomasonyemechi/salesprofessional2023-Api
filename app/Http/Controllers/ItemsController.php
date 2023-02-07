<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ItemsController extends Controller
{
    public function searchItem(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            's' => 'required|string',
        ]);
        if ($validatedData->fails()) {
            return response(['errors' => $validatedData->errors()->all()], 422);
        }  
        $q = $request->s;
        $check = Item::where(['business_id' => $this->bid()])->
        where('name', 'like', "%{$q}%")->orwhere('bar_code', 'like', "%{$q}%")->orwhere('description', 'like', "%{$q}%")->get(['id', 'name', 'stock_value']); 

        if(count($check) == 0) {
            return response([
                'message' => 'No match was found for item'
            ], 404);
        }
        
        return response([
            'message' => 'Match found for item',
            'data' => $check
        ], 200);
    }

    public function getItemsForSale()
    {
        return Item::where(['business_id' => $this->bid()])->orderby('name', 'asc')->limit(200)->get([
            'id', 'name', 'stock_value'
        ]);
    }

    public function getItemsList()
    {
        return Item::where(['business_id' => $this->bid()])->orderby('updated_at', 'asc')->paginate(200);
    }

    public function updateItem(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'string',
            'item_id' => 'required|exists:items,id',
            'category_id' => 'exists:product_categories,id',
            'min_indicator' => 'required|integer',
            'max_indicator' => 'required|integer',
            'selling_price' => 'required|integer',
            'bar_code' => 'required|string',
        ]);
        if ($validatedData->fails()) {
            return response(['errors' => $validatedData->errors()->all()], 422);
        }

        Item::where('id', $request->item_id)->update([
            'product_category_id' => $request->category_id,
            'name' => $request->name,
            'description' => $request->description,
            'min_indicator' => $request->min_indicator,
            'max_indicator' => $request->max_indicator,
            'selling_price' => $request->selling_price,
            'bar_code' => $request->bar_code
        ]);
        return response([
            'message' => 'Item('.$request->name.') has been updated'
        ], 200);
    }

    public function createItem(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'string',
            'category_id' => 'required|exists:product_categories,id',
        ]);
        if ($validatedData->fails()) {
            return response(['errors' => $validatedData->errors()->all()], 422);
        }

        $check  = Item::where(['business_id' => $this->bid(), 'name' => $request->name])->count();
        if($check > 0) {
            return response([
                'message' => 'Item already exists, try another name'
            ], 409);
        }

        Item::create([
            'business_id' => $this->bid(),
            'uuid' => Str::uuid()->toString(),
            'product_category_id' => $request->category_id,
            'name' => $request->name,
            'description' => $request->description,
            'min_indicator' => $request->min_indicator ?? 0,
            'max_indicator' => $request->max_indicator ?? 0,
            'stock_value' => 0,
            'selling_price' => $request->selling_price ?? 0,
            'bar_code' => $request->bar_code
        ]);
        
        return response([
            'message' => 'Item ('.$request->name.') has been crated sucessfuly'
        ], 200);
    }
}
