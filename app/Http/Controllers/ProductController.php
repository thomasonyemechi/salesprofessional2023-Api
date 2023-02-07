<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function getProductCategory()
    {
        return ProductCategory::where(['business_id' => $this->bid()])->paginate(200, ['id','name','description']); 
    }

    public function updateCategory(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'category_id' => 'required|exists:product_categories,id',
            'name' => 'string|required',
            'description' => 'string',
        ]);
        if ($validatedData->fails()) {
            return response(['errors' => $validatedData->errors()->all()], 422);
        }
        ProductCategory::where('id', $request->category_id)->update([
            'name' => $request->name,
            'description' => $request->description
        ]);
        return response([
            'message' => 'Product category('.$request->name.') has been updated scuessfully'
        ], 200);
    }

    public function createProductCategory(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'name' => 'string|required',
            'description' => 'string',
        ]);
        if ($validatedData->fails()) {
            return response(['errors' => $validatedData->errors()->all()], 422);
        }
        $check = ProductCategory::where(['business_id' => $this->bid(), 'name' => $request->name])->count();
        if($check > 0) {
            return response([
                'message' => 'This category already exists, try another'
            ], 409);
        }

        ProductCategory::create([
            'business_id' => $this->bid(),
            'name' => $request->name,
            'description' => $request->description
        ]);

        return response([
            'message' => 'Product category('.$request->name.') has been created'
        ], 200);
    }
}
