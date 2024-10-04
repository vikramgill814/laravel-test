<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SaleController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::redirect('/dashboard', '/sales');

// Route to display the coffee sales form page
Route::get('/sales', [SaleController::class, 'index'])->middleware(['auth'])->name('coffee.sales');

// Route to calculate and record the sale
Route::post('/sales/{productId}/calculate', [SaleController::class, 'calculateSale'])->middleware(['auth']);
Route::post('/sales/{productId}/record-sale', [SaleController::class, 'recordSale']);
Route::get('/sales/data', [SaleController::class, 'getSales'])->name('sales.data');
Route::get('/shipping-partners', function () {
    return view('shipping_partners');
})->middleware(['auth'])->name('shipping.partners');

require __DIR__.'/auth.php';
