<?php

namespace App\Http\Controllers\API;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\TransactionItem;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function all(Request $request) {
        $id     = $request->input('id');
        $limit  = $request->input('limit');
        $status = $request->input('status');
        $transaction = Transaction::with(['items.product'])->find($id);
        if($transaction) {
            return ResponseFormatter::success($transaction, 'Data Berhasil Diambil');
        } else {
            return ResponseFormatter::error(null, 'Transaksi tidak ada');
        }
    }

    public function checkout(Request $request) {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'exists:products,id',
            'total_price' => 'required',
            'shipping_price' => 'required|in:PENDING,SUCCESS,CANCELLED,FAILED,SHIPPING,SHIPPED'
        ]);

        $transaction = Transaction::create([
            'user_id' => Auth::user()->id,
            'address' => $request->address,
            'total_price' => $request->total_price,
            'shipping_price' => $request->shipping_price,
            'status' => $request->status,
        ]);

        foreach($request->items as $product) {
            TransactionItem::create([
                'user_id' => Auth::user()->id,
                'product_id' => $product['id'],
                'transaction_id' => $transaction->id,
                'quantity' => $product['quantity']
            ]);
        }
        return ResponseFormatter::success($transaction->load('item, product'), 'Trnsaksi Berhasil');
    }
}
