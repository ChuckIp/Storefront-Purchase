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

        // Lock the product to prevent multiple users buy last item at same time
        $product = Product::lockForUpdate()->find($request->product_id);

        // Product out of stock
        if ($product->stock <= 0) {
            return response()->json([
                'error' => 'Product out of stock'
            ], 422);
        }

        // Transaction guarantee we don't reduce stock without decrementing stock
        $purchase = DB::transaction(function () use ($user, $product) {
            $product->decrement('stock');

            return Purchase::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'price' => $product->price,
            ]);
        });

        // Dispatch event
        PurchaseCompleted::dispatch($purchase);

        return response()->json($purchase, 201);
    }
}
