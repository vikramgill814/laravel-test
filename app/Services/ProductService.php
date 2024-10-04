<?php

namespace App\Services;

use App\Repositories\ProductRepository;

class ProductService
{
    protected $productRepository;
    protected $saleRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function getProducts($productId)
    {
       
        $products = $this->productRepository->findProducts($productId);

        if (!$products) {
            throw new \Exception("Product not found");
        }
        return $products;
      
    }
   
}
