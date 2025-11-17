<?php

namespace App\Http\Controllers;

// Laravel
use Illuminate\Http\Request;

// Requests
use App\Http\Requests\StorePurchaseRequest;
use Illuminate\Support\Facades\DB;

// Models
use App\Models\User;
use App\Models\Product;
use App\Models\Purchase;

// Events
use App\Events\PurchaseCompleted;

class PurchaseController extends Controller
{
    public function store(StorePurchaseRequest $request)
    {
        $user = User::find($request->user_id);
        $product = Product::lockForUpdate()->find($request->product_id);

        if ($product->stock <= 0) {
            return response()->json([
                'error' => 'Product out of stock'
            ], 422);
        }

        $purchase = DB::transaction(function () use ($user, $product) {

            $product->decrement('stock');

            return Purchase::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'price' => $product->price,
            ]);
        });

        PurchaseCompleted::dispatch($purchase);

        return response()->json($purchase, 201);
    }
}
