<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Restock;
use App\Models\RestockSummary;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StockController extends Controller
{

    function getInfoByInvoiceNo($invoice)
    {
        $restockSummary = RestockSummary::with(['supplier:id,fullname,phone', 'restock_items.item:id,name,stock_value,selling_price'])->where('invoice_no', $invoice)->first();
        if ($restockSummary->business_id != $this->bid()) {
            return response([
                'message' => 'Your cannot access the content',
            ], 403);
        }


        $restockSummary->makeHidden(['created_at', 'updated_at']);
        return response([
            $restockSummary,
        ], 200);

        // $authors = Author::with(['books' => fn ($query) => $query->where('title', 'like', 'PHP%')])
        //     ->whereHas(
        //         'books',
        //         fn ($query) =>
        //         $query->where('title', 'like', 'PHP%')
        //     )
        //     ->get();
    }
    /*
        $items = [
            [
                'item_id' => 41, 'quantity' => 20, 'buying_cost' => 3700,
                'selling_price' => 5500, 'batch_no' => '282382383839389', 
                'expiry_date' => '12/2/22',
                ''
            ]
        ]


        stock types
        1 == restock 
    */

    function restockItem(Request $request)
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
            [
                'item_id' => 1, 'quantity' => 20, 'buying_cost' => 750,
                'selling_price' => 1250, 'batch_no' => '282382383839389',
                'expiry_date' => '12/2/22',
            ],
            [
                'item_id' => 3, 'quantity' => 50, 'buying_cost' => 325,
                'selling_price' => 570, 'batch_no' => '2823823838389',
                'expiry_date' => '12/2/23',
            ],
            [
                'item_id' => 2, 'quantity' => 15, 'buying_cost' => 1500,
                'selling_price' => 2000, 'batch_no' => '282382838389',
                'expiry_date' => '12/9/23',
            ],
        ];

        $supplier_id = $request->supplier_id;
        $invoice = $this->generateHash(15);
        $restockSummary = RestockSummary::create([
            'business_id' => $this->bid(),
            'discount' => $request->discount,
            'amount' => 0,
            'total' => 0,
            'invoice_no' => $invoice,
            'supplier_id' => $supplier_id
        ]);

        $total = 0;
        $restockSummary_id = $restockSummary->id;
        foreach ($items as $item) {
            $amt = $item['quantity'] * $item['buying_cost'];
            $total += $amt;
            $item_id = $item['item_id'];
            $restock = Restock::create([
                'summary_id' => $restockSummary_id,
                'item_id' => $item_id,
                'buying_cost' => $item['buying_cost'],
                'selling_price' => $item['selling_price'],
                'batch_no' => $item['batch_no'],
                'expiry_date' => $item['expiry_date'],
                'quantity' => $item['quantity'],
            ]);

            Stock::create([
                'item_id' => $item_id,
                'stock_id' => $restock->id,
                'stock_type' => 1,
                'quantity' => $item['quantity'],
            ]);
            $selling_price = $item['selling_price'];
            $item = Item::find($item_id);
            $item->update([
                'selling_price' => $selling_price,
                'stock_value' => $item->stock->sum('quantity'),
            ]);
        }


        $restockSummary->update([
            'amount' => $total,
            'total' => $total - $request->discount
        ]);

        return response([
            'message' => 'Restock was sucessful',
            'invoice_no' => $invoice
        ]);
    }
}
