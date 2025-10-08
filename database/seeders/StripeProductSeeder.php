<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\StripeProduct;
use App\Models\StripePrice;

class StripeProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
     public function run(): void
    {
        $basic = StripeProduct::create([
            'name' => 'Gold',
            'stripe_product_id' => 'prod_TBE3OwftafqB2X',
            'description' => 'Access to basic features',
        ]);

        $pro = StripeProduct::create([
            'name' => 'Silver',
            'stripe_product_id' => 'prod_TBE5E1dxUy2s3L',
            'description' => 'Access to all premium features',
        ]);

        StripePrice::create([
            'product_id' => $basic->id,
            'stripe_price_id' => 'price_1SErbnSDYrQIdbO59VcwKIrM',
            'unit_amount' => 10,
            'currency' => 'inr',
            'interval' => 'month',
        ]);

        StripePrice::create([
            'product_id' => $basic->id,
            'stripe_price_id' => 'price_1SErd0SDYrQIdbO5nZVVm96r',
            'unit_amount' => 20,
            'currency' => 'inr',
            'interval' => 'month',
        ]);

        StripePrice::create([
            'product_id' => $pro->id,
            'stripe_price_id' => 'price_1SErdhSDYrQIdbO5UYWGOk4h',
            'unit_amount' => 30,
            'currency' => 'inr',
            'interval' => 'month',
        ]);
    }
}
