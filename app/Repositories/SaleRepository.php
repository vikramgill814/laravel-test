<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class SaleRepository
{
    public function recordSale($productId, $quantity, $unitCost, $sellingPrice)
    {
        return DB::table('sales')->insert([
            'product_id' => $productId,
            'quantity' => $quantity,
            'unit_cost' => $unitCost,
            'selling_price' => $sellingPrice,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
    // Method to fetch sales data with pagination and searching
    public function getSalesData($request)
    {
        // Extract pagination and search parameters
        $start = (int) $request->get('start');
        $limit = (int) $request->get('length');
        $txt_search = $request->get('search')['value'] ?? NULL;

        // Create the query for the sales table
        $query = DB::table('sales as s')
            ->join('products as p', 's.product_id', '=', 'p.id') // Join to get product details
            ->select('s.*', 'p.name as product_name'); // Select sales details and product name

        // Apply search filter
        if ($txt_search != '') {
            $query->where(function ($query) use ($txt_search) {
                $query->orWhere('p.name', 'like', '%' . $txt_search . '%')
                      ->orWhere('s.quantity', 'like', '%' . $txt_search . '%')
                      ->orWhere('s.unit_cost', 'like', '%' . $txt_search . '%')
                      ->orWhere('s.selling_price', 'like', '%' . $txt_search . '%')
                      ->orWhere('s.created_at', 'like', '%' . $txt_search . '%'); // Use created_at for filtering sold_date
            });
        }
        if(!empty($request->productId))
            $query->where('s.product_id',$request->productId);
        
        // Get total records without filtering
        $total = with(clone $query)->count();

        // Get records with pagination
        $sales = $query->orderBy('s.created_at', 'desc') // Order by creation date descending
            ->limit($limit)
            ->offset($start)
            ->get();

        return [
            'total' => $total,
            'sales' => $sales,
        ];
    }
}
