<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use Illuminate\Http\Request;
use App\Services\SaleService;


class SaleController extends Controller
{
    protected $saleService;

    protected $productService;

    /**
     * This feature adopts a forward-looking approach. 
     * In Part 1, we enable this feature because the company currently offers only one product. 
     * However, we anticipate that more coffee products may be added in the future. 
     * By setting this flag to false, we can easily activate all products when necessary.
     * 
     * @var bool $defaultCoffeeFeature
     * - true: Only for the default product (Golden Coffee).
     * - false: Applicable for all products.
     * */
    protected $defaultCoffeeFeature = True;
    /**
     * @var int $defaultProductId
     * The ID of the default product (Golden Coffee).
     */
    protected $defaultProductId = 1;
    /**
     * Constructor for initializing services.
     *
     * @param ProductService $productService The product service instance.
     * @param SaleService $saleService The sale service instance.
     */
    public function __construct(ProductService $productService, SaleService $saleService)
    {
        $this->saleService = $saleService;
        $this->productService = $productService;
    }

    public function index()
    {
        $defaultProductId = $this->defaultCoffeeFeature ? $this->defaultProductId : NULL;

        $products = $this->productService->getProducts($defaultProductId);

        return view('coffee_sales', compact('products', 'defaultProductId'));
    }

    public function calculateSale(Request $request, $productId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'unit_cost' => 'required|numeric|min:0',
        ]);

        $sellingPrice = $this->saleService->calculateSellingPrice(
            $request->input('quantity'),
            $request->input('unit_cost'),
            $productId
        );

        return response()->json(['selling_price' => $sellingPrice]);
    }
    // Record the sale in the database
    public function recordSale(Request $request, $productId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'unit_cost' => 'required|numeric|min:0',
        ]);

        // Use the SaleService to record the sale
        $result = $this->saleService->recordSale(
            $productId,
            $request->input('quantity'),
            $request->input('unit_cost'),
            $request->input('selling_price')
        );

        return response()->json(['success' => true]);
    }

    // Fetch previous sales for DataTable with server-side pagination
    public function getSales(Request $request)
    {
        if ($request->ajax()) {

            $defaultProductId = $this->defaultCoffeeFeature ? $this->defaultProductId : NULL;
            $request->merge(["productId" => $defaultProductId]);

            return $this->saleService->getSales($request);
        }
    }
}
