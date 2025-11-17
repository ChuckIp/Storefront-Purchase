<?php

namespace Database\Seeders;

// Laravel
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

// Models
use App\Models\User;
use App\Models\Product;
use App\Models\Purchase;

class PurchasesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $products = Product::all();

        // Ensure we have something to relate
        if ($users->isEmpty() || $products->isEmpty()) {
            $this->command->warn('Users or products missing — skipping purchases seeding.');
            return;
        }

        Purchase::factory()
            ->count(40)
            ->make()
            ->each(function ($purchase) use ($users, $products) {
                $purchase->user_id = $users->random()->id;
                $purchase->product_id = $products->random()->id;
                $purchase->save();
            });
    }
}
