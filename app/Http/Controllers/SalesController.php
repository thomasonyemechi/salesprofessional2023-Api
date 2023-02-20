<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Item;
use App\Models\Sales;
use App\Models\SalesSummary;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SalesController extends Controller
{
    function fetchCustomerPurchases($customer_id)
    {
        $customer = Customer::find($customer_id);
        if($customer->business_id != $this->bid()){
            return response([
                'message' => 'You cannot access the info'
            ], 403);
        }

        $customer;

        return;
    }

    function makeSlaes(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'supplier_id' => 'required|integer|exists:suppliers,id',
            'total' => 'required|integer',
            'discount' => 'required|integer',
        ]);
        if ($validatedData->fails()) {
            return response(['errors' => $validatedData->errors()->all()], 422);
        }

        $items = [
            ['item_id' => 1, 'quantity' => 1, 'price' => 1250,],
            ['item_id' => 3, 'quantity' => 3, 'price' => 1000,],
            ['item_id' => 2, 'quantity' => 4, 'price' => 1750,],
        ];

        $customer_id = $request->customer_id;
        $invoice = $this->generateHash(15);
        $saleSummary = SalesSummary::create([
            'business_id' => $this->bid(),
            'discount' => $request->discount,
            'amount' => 0,
            'total' => 0,
            'invoice_no' => $invoice,
            'customer_id' => $customer_id
        ]);

        $total = 0;
        $saleSummary_id = $saleSummary->id;
        foreach ($items as $item) {
            $amt = $item['quantity'] * $item['price'];
            $total += $amt;
            $item_id = $item['item_id'];

            $restock = Sales::create([
                'summary_id' => $saleSummary_id,
                'item_id' => $item_id,
                'amount' => $item['price'],
                'quantity' => $item['quantity'],
            ]);

            Stock::create([
                'item_id' => $item_id,
                'stock_id' => $restock->id,
                'stock_type' => 1,
                'quantity' => $item['quantity'],
            ]);

            ////updating stock value on the items table
            $item = Item::find($item_id);
            $item->update([
                'stock_value' => $item->stock->sum('quantity'),
            ]);
        }

        /// updating sales summary_info
        $saleSummary->update([
            'amount' => $total,
            'total' => $total - $request->discount
        ]);

        return response([
            'message' => 'Sales has been logged sucessfuly!',
            'invoice_no' => $invoice
        ]);
    }
}
