<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductRepository
{
    public function findById($id)
    {
        return DB::table('products')->where('id', $id)->first();
    }
    public function findProducts($id)
    {
        if (!empty($id))
            return Product::where('id',$id)->get();
        else
            // Fetch all products from the database
            return Product::all();
    }
}
