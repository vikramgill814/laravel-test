<?php

namespace App\Services;

use App\Repositories\SaleRepository;
use App\Repositories\ProductRepository;

class SaleService
{
    protected $productRepository;
    protected $saleRepository;

    public function __construct(ProductRepository $productRepository, SaleRepository $saleRepository)
    {
        $this->productRepository = $productRepository;
        $this->saleRepository = $saleRepository;
    }

    public function calculateSellingPrice($quantity, $unitCost, $productId)
    {
        $product = $this->productRepository->findById($productId);
       
        if (!$product) {
            throw new \Exception("Product not found");
        }

        $cost = $quantity * $unitCost;
        $sellingPrice = ($cost / (1 - $product->profit_margin)) + $product->shipping_cost;


        return $sellingPrice;
    }
    // Record the sale in the database
    public function recordSale($productId, $quantity, $unitCost)
    {
        $sellingPrice = $this->calculateSellingPrice($quantity, $unitCost, $productId);

        // Record the sale
        return $this->saleRepository->recordSale($productId, $quantity, $unitCost, $sellingPrice);
    }
    // Fetch previous sales for DataTable with server-side pagination
    public function getSales($request)
    {
        if ($request->ajax()) {
            $salesData = $this->saleRepository->getSalesData($request); // Fetch sales data from the repository

            $data = [];
            foreach ($salesData['sales'] as $sale) {
                $row = [];
                if(empty($request->productId))
                    $row[] = $sale->product_name;  // Product name
                $row[] = $sale->quantity;       // Quantity sold
                $row[] = '£' . number_format($sale->unit_cost, 2); // Unit cost
                $row[] = '£' . number_format($sale->selling_price, 2); // Selling price
                $row[] = $sale->created_at; // Sold date (created_at)

                $data[] = $row;
            }

            // Prepare output for DataTables
            return response()->json([
                "draw" => intval($request->get('draw')),
                "recordsTotal" => $salesData['total'],
                "recordsFiltered" => $salesData['total'],
                "data" => $data,
            ]);
        }
    }
}
