<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('products')->insert([
            [
                'name' => 'Gold Coffee',
                'profit_margin' => 0.25,
                'shipping_cost' => 10.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Arabic Coffee',
                'profit_margin' => 0.15,
                'shipping_cost' => 10.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
