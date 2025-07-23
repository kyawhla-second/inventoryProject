<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RawMaterialController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\StaffDailyChargeController;
use App\Http\Controllers\ProfitLossController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\RawMaterialUsageController;

Auth::routes();

// Language switcher
Route::get('lang/{locale}', function ($locale) {
    if (! in_array($locale, ['en', 'mm'])) {
        abort(400);
    }
    session(['locale' => $locale]);
    return redirect()->back()->withCookie(cookie('locale', $locale, 60*24*365)); // 1 year
})->name('lang.switch');

Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/goal', [DashboardController::class, 'updateGoal'])->name('dashboard.goal');

    Route::middleware('role:admin,staff')->group(function () {
        Route::resource('products', ProductController::class);
        Route::resource('purchases', PurchaseController::class);
        Route::resource('sales', SaleController::class);
        Route::resource('customers', CustomerController::class);
        Route::resource('raw-materials', RawMaterialController::class);
        Route::get('raw-materials-low-stock', [RawMaterialController::class, 'lowStock'])->name('raw-materials.low-stock');
        
        // Raw Material Usage Tracking
        Route::resource('raw-material-usages', RawMaterialUsageController::class);
        Route::get('raw-material-usages-bulk/create', [RawMaterialUsageController::class, 'bulkCreate'])->name('raw-material-usages.bulk-create');
        Route::post('raw-material-usages-bulk/store', [RawMaterialUsageController::class, 'bulkStore'])->name('raw-material-usages.bulk-store');
        Route::get('raw-materials/{rawMaterial}/usage-stats', [RawMaterialUsageController::class, 'getUsageStats'])->name('raw-materials.usage-stats');
        Route::resource('orders', OrderController::class);
        Route::post('orders/{order}/convert-to-sale', [OrderController::class, 'convertToSale'])->name('orders.convert-to-sale');
        Route::get('orders/{order}/create-purchase', [OrderController::class, 'createPurchaseForm'])->name('orders.create-purchase-form');
        Route::post('orders/{order}/create-purchase', [OrderController::class, 'createPurchase'])->name('orders.create-purchase');
        
        // Invoices
        Route::resource('invoices', InvoiceController::class);
        Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'generatePdf'])->name('invoices.pdf');
        Route::get('invoices/{invoice}/print', [InvoiceController::class, 'print'])->name('invoices.print');
        Route::post('invoices/{invoice}/send-to-printer', [InvoiceController::class, 'sendToPrinter'])->name('invoices.send-to-printer');
        Route::get('invoices/{invoice}/preview', [InvoiceController::class, 'preview'])->name('invoices.preview');
        Route::post('invoices/create-from-sale/{sale}', [InvoiceController::class, 'createFromSale'])->name('invoices.create-from-sale');
        Route::post('invoices/create-from-order/{order}', [InvoiceController::class, 'createFromOrder'])->name('invoices.create-from-order');
        Route::patch('invoices/{invoice}/mark-paid', [InvoiceController::class, 'markAsPaid'])->name('invoices.mark-paid');
        Route::patch('invoices/{invoice}/mark-sent', [InvoiceController::class, 'markAsSent'])->name('invoices.mark-sent');
        Route::get('api/printers', [InvoiceController::class, 'getPrinters'])->name('api.printers');
    });

    Route::middleware('role:admin')->group(function () {
        Route::resource('categories', CategoryController::class);
        Route::resource('suppliers', SupplierController::class);

        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        Route::post('reports/sales', [ReportController::class, 'salesReport'])->name('reports.sales');
        
        // Staff Daily Charges
        Route::resource('staff-charges', StaffDailyChargeController::class);
        Route::patch('staff-charges/{staffCharge}/approve', [StaffDailyChargeController::class, 'approve'])->name('staff-charges.approve');
        Route::patch('staff-charges/{staffCharge}/mark-paid', [StaffDailyChargeController::class, 'markAsPaid'])->name('staff-charges.mark-paid');
        
        // Profit & Loss Statements
        Route::resource('profit-loss', ProfitLossController::class);
        Route::get('profit-loss-quick', [ProfitLossController::class, 'quickReport'])->name('profit-loss.quick');
        Route::patch('profit-loss/{profitLoss}/finalize', [ProfitLossController::class, 'finalize'])->name('profit-loss.finalize');
    });
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
