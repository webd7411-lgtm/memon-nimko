<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ZoneController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\ManuallController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\NarrationController;
use App\Http\Controllers\ReportingController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\SubcategoryController;
use App\Http\Controllers\AccountsHeadController;
use App\Http\Controllers\SalesOfficerController;
use App\Http\Controllers\StockTransferController;
use App\Http\Controllers\InwardgatepassController;
use App\Http\Controllers\ProductBookingController;
use App\Http\Controllers\WarehouseStockController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\StockAdjustmentController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\RawMaterialController;
use App\Http\Controllers\DistributorAdminController;
use App\Http\Controllers\DistributorPortalController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\AssetCategoryController;

/*
    |--------------------------------------------------------------------------
    | Web Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register web routes for your application. These
    | routes are loaded by the RouteServiceProvider and all of them will
    | be assigned to the "web" middleware group. Make something great!
    |
    */

Route::get('/home', [HomeController::class, 'index'])->middleware('auth')->name('home');

Route::get('System/Reports', [HomeController::class, 'System_Reports'])->name('System.Reports')->middleware('permission:System Reports');
Route::get('System/Reports/PDF', [HomeController::class, 'System_Reports_PDF'])->name('System.Reports.PDF')->middleware('permission:System Reports');
Route::get('System/Reports/PDF-BW', [HomeController::class, 'System_Reports_PDF_BW'])->name('System.Reports.PDF.BW')->middleware('permission:System Reports');
Route::get('/category-products/{id}', [HomeController::class, 'categoryProducts']);

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Settings
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index')->middleware('permission:Settings');
    Route::post('/settings/update', [SettingController::class, 'update'])->name('settings.update')->middleware('permission:Settings');

    // Category Routes
    Route::get('/category', [CategoryController::class, 'index'])->name('Category.home')->middleware('permission:Category');
    Route::delete('/category/delete/{id}', [CategoryController::class, 'delete'])->name('delete.category')->middleware('permission:Category');
    Route::post('/category/store', [CategoryController::class, 'store'])->name('store.category')->middleware('permission:Category');

    // Table Routes
    Route::get('/tables', [TableController::class, 'index'])->name('table.index')->middleware('permission:Tables');
    Route::post('/table/store', [TableController::class, 'store'])->name('table.store')->middleware('permission:Tables');
    Route::delete('/table/delete/{id}', [TableController::class, 'delete'])->name('table.delete')->middleware('permission:Tables');

    // Brand Routes
    Route::get('/Brand', [BrandController::class, 'index'])->name('Brand.home')->middleware('permission:Brands');
    Route::delete('/Brand/delete/{id}', [BrandController::class, 'delete'])->name('delete.Brand')->middleware('permission:Brands');
    Route::post('/Brand/store', [BrandController::class, 'store'])->name('store.Brand')->middleware('permission:Brands');

    // Unit Routes
    Route::get('/Unit', [UnitController::class, 'index'])->name('Unit.home')->middleware('permission:Units');
    Route::delete('/Unit/delete/{id}', [UnitController::class, 'delete'])->name('delete.Unit')->middleware('permission:Units');
    Route::post('/Unit/store', [UnitController::class, 'store'])->name('store.Unit')->middleware('permission:Units');

    // Subcategory Routes
    Route::get('/subcategory', [SubcategoryController::class, 'index'])->name('subcategory.home')->middleware('permission:Sub Category');
    Route::delete('/subcategory/delete/{id}', [SubcategoryController::class, 'delete'])->name('delete.subcategory')->middleware('permission:Sub Category');
    Route::post('/subcategory/store', [SubcategoryController::class, 'store'])->name('store.subcategory')->middleware('permission:Sub Category');

    // Product Routes
    Route::get('/Product', [ProductController::class, 'product'])->name('product')->middleware('permission:Products');
    Route::get('/create_prodcut', [ProductController::class, 'view_store'])->name('store');
    Route::get('/create_product', [ProductController::class, 'view_store'])->name('product.create');
    Route::post('/store-product', [ProductController::class, 'store_product'])->name('store-product');
    Route::put('/product/update/{id}', [ProductController::class, 'update'])->name('product.update');
    Route::get('/get-subcategories/{category_id}', [ProductController::class, 'getSubcategories'])->name('fetch-subcategories');
    Route::get('/generate-barcode-image', [ProductController::class, 'generateBarcode'])->name('generate-barcode-image');
    Route::get('/barcode/{id}', [ProductController::class, 'barcode'])->name('product.barcode');
    Route::get('/get-all-products-for-search', [ProductController::class, 'getAllProductsForSearch'])->name('get-all-products-for-search');
    Route::post('/products/reset-stock', [ProductController::class, 'resetStock'])->name('products.reset_stock');
    Route::get('/products/all-ids', [ProductController::class, 'getAllProductIds'])->name('products.all-ids');
    Route::post('/products/bulk-edit-store', [ProductController::class, 'bulkEditStore'])->name('products.bulk-edit-store');
    Route::get('/products/bulk-edit', [ProductController::class, 'bulkEdit'])->name('products.bulk-edit');
    Route::post('/products/bulk-update', [ProductController::class, 'bulkUpdate'])->name('products.bulk-update');
    Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::get('/products/{id}/bom', [ProductController::class, 'getBom'])->name('products.bom');
    Route::post('/products/bom/store', [ProductController::class, 'storeBom'])->name('products.bom.store');
    Route::get('/products/{id}/bom-raw-materials', [ProductionController::class, 'getBomRawMaterials'])->name('products.bom.raw-materials');

    // Discount Routes
    Route::prefix('discount')->group(function () {
        Route::get('/', [DiscountController::class, 'index'])->name('discount.index')->middleware('permission:Discount Products');
        Route::get('/create', [DiscountController::class, 'create'])->name('discount.create');
        Route::post('/store', [DiscountController::class, 'store'])->name('discount.store');
        Route::post('/toggle-status/{id}', [DiscountController::class, 'toggleStatus'])->name('discount.toggleStatus');
        Route::get('/barcode/{id}', [DiscountController::class, 'barcode'])->name('discount.barcode');
    });

    // Customer Routes
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index')->middleware('permission:Customer');
    Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create');
    Route::post('/customers/store', [CustomerController::class, 'store'])->name('customers.store');
    Route::get('/customers/edit/{id}', [CustomerController::class, 'edit'])->name('customers.edit');
    Route::delete('/customers/delete/{id}', [CustomerController::class, 'destroy'])->name('customers.destroy')->middleware('permission:Customer');
    Route::put('customers/{id}', [CustomerController::class, 'update'])->name('customers.update');

    // Customer New Routes
    Route::get('/customers/inactive', [CustomerController::class, 'inactiveCustomers'])->name('customers.inactive');
    Route::get('/customers/inactive/{id}', [CustomerController::class, 'markInactive'])->name('customers.markInactive');
    Route::get('customers/toggle-status/{id}', [CustomerController::class, 'toggleStatus'])->name('customers.toggleStatus');
    Route::get('/customers/ledger', [CustomerController::class, 'customer_ledger'])->name('customers.ledger');
    Route::get('/customer/payments', [CustomerController::class, 'customer_payments'])->name('customer.payments');
    Route::post('/customer/payments', [CustomerController::class, 'store_customer_payment'])->name('customer.payments.store');
    Route::get('/customer/payments/{id}/edit', [CustomerController::class, 'edit_customer_payment'])->name('customer.payments.edit');
    Route::put('/customer/payments/{id}', [CustomerController::class, 'update_customer_payment'])->name('customer.payments.update');
    Route::get('/customer/ledger/{id}', [CustomerController::class, 'getCustomerLedger']);
    Route::delete('/customer-payments/{id}', [CustomerController::class, 'destroy_payment'])->name('customer.payments.destroy');
    Route::get('customer-payments/receipt/{id}', [CustomerController::class, 'customer_payment_receipt'])->name('customer.payments.receipt');
    Route::get('/customer/credit-info/{id}', [CustomerController::class, 'getCreditInfo'])->name('customer.credit.info');
    Route::get('/customers/getByType', [CustomerController::class, 'getByType'])->name('customers.byType');

    // Vendor Routes
    Route::get('/vendors', [VendorController::class, 'index'])->name('vendors')->middleware('permission:Vendor');
    Route::post('/vendor/store', [VendorController::class, 'store'])->name('vendor.store');
    Route::delete('/vendor/delete/{id}', [VendorController::class, 'delete'])->name('vendor.delete')->middleware('permission:Vendor');
    Route::get('/vendors-ledger', [VendorController::class, 'vendors_ledger'])->name('vendors-ledger');
    Route::get('/vendor/payments', [VendorController::class, 'vendor_payments'])->name('vendor.payments');
    Route::post('/vendor/payments', [VendorController::class, 'store_vendor_payment'])->name('vendor.payments.store');
    Route::get('/vendor/payments/{id}/edit', [VendorController::class, 'edit_vendor_payment'])->name('vendor.payments.edit');
    Route::put('/vendor/payments/{id}', [VendorController::class, 'update_vendor_payment'])->name('vendor.payments.update');
    Route::delete('/vendor/payment/delete/{id}', [VendorController::class, 'destroy_payment'])->name('vendor.payment.delete');
    Route::get('/vendor/bilties', [VendorController::class, 'vendor_bilties'])->name('vendor.bilties');
    Route::post('/vendor/bilties', [VendorController::class, 'store_vendor_bilty'])->name('vendor.bilties.store');
    Route::get('vendor/payment-receipt/{id}', [VendorController::class, 'printReceipt'])->name('vendor.payment.receipt');

    // Warehouse Routes
    Route::get('/warehouse', [WarehouseController::class, 'index'])->name('warehouses.index')->middleware('permission:List Warehouse');
    Route::post('/warehouse/store', [WarehouseController::class, 'store'])->middleware('permission:List Warehouse');
    Route::delete('/warehouse/delete/{id}', [WarehouseController::class, 'delete'])->middleware('permission:List Warehouse');

    // Branches
    Route::resource('branch', BranchController::class)->names('branch')->only(['index', 'store'])->middleware('permission:Branch');
    Route::delete('/branch/delete/{id}', [BranchController::class, 'delete'])->name('branch.delete')->middleware('permission:Branch');
    Route::post('/switch-branch', [BranchController::class, 'switchBranch'])->name('branch.switch');

    // Roles
    Route::resource('roles', RoleController::class)->names('roles')->only(['index', 'store'])->middleware('permission:Roles');
    Route::delete('/roles/delete/{id}', [RoleController::class, 'delete'])->name('roles.delete')->middleware('permission:Roles');
    Route::post('/admin/roles/update-permission', [RoleController::class, 'updatePermissions'])->name('roles.update.permission')->middleware('permission:Roles');

    // Permissions
    Route::resource('permissions', PermissionController::class)->names('permissions')->only(['index', 'store'])->middleware('permission:Permissions');
    Route::delete('/permissions/delete/{id}', [PermissionController::class, 'delete'])->name('permission.delete')->middleware('permission:Permissions');

    // Users
    Route::resource('users', UserController::class)->names('users')->only(['index', 'store'])->middleware('permission:Users');
    Route::delete('/users/delete/{id}', [UserController::class, 'delete'])->name('users.delete')->middleware('permission:Users');
    Route::post('/admin/users/update-roles', [UserController::class, 'updateRoles'])->name('users.update.roles')->middleware('permission:Users');
    Route::post('/users/opening-balance', [UserController::class, 'storeOpeningBalance'])->name('users.store_opening_balance')->middleware('permission:Users');

    // Zone
    Route::get('zone', [ZoneController::class, 'index'])->name('zone.index')->middleware('permission:Zone');
    Route::post('zones/store', [ZoneController::class, 'store'])->name('zone.store');
    Route::get('zones/edit/{id}', [ZoneController::class, 'edit'])->name('zone.edit');
    Route::delete('zones/delete/{id}', [ZoneController::class, 'destroy'])->name('zone.delete')->middleware('permission:Zone');

    // Sales Officer
    Route::get('sales-officers', [SalesOfficerController::class, 'index'])->name('sales.officer.index')->middleware('permission:Sales Officer');
    Route::post('sales-officers/store', [SalesOfficerController::class, 'store'])->name('sales-officer.store');
    Route::get('sales-officers/edit/{id}', [SalesOfficerController::class, 'edit'])->name('sales.officer.edit');
    Route::delete('sales-officers/{id}', [SalesOfficerController::class, 'destroy'])->name('sales-officer.delete');

    // Purchase Routes
    Route::get('/Purchase', [PurchaseController::class, 'index'])->name('Purchase.home')->middleware('permission:Purchase');
    Route::get('/add/Purchase', [PurchaseController::class, 'add_purchase'])->name('add_purchase');
    Route::post('/Purchase/store', [PurchaseController::class, 'store'])->name('store.Purchase');
    Route::get('/purchase/{id}/edit', [PurchaseController::class, 'edit'])->name('purchase.edit');
    Route::put('/purchase/{id}', [PurchaseController::class, 'update'])->name('purchase.update');
    Route::delete('/purchase/{id}', [PurchaseController::class, 'destroy'])->name('purchase.destroy');

    // Production Routes
    Route::get('/production', [ProductionController::class, 'index'])->name('production.index')->middleware('permission:Production|Purchase');
    Route::get('/production/create', [ProductionController::class, 'create'])->name('production.create');
    Route::post('/production/store', [ProductionController::class, 'store'])->name('production.store');
    Route::get('/production/{id}/edit', [ProductionController::class, 'edit'])->name('production.edit');
    Route::put('/production/{id}', [ProductionController::class, 'update'])->name('production.update');
    Route::get('/production/{id}/gatepass', [ProductionController::class, 'gatepass'])->name('production.gatepass');

    // Raw Material Routes (old - to be removed after migration)
    Route::get('/raw-materials', [RawMaterialController::class, 'index'])->name('raw-materials.index')->middleware('permission:Raw Materials|Purchase');
    Route::post('/raw-materials/store', [RawMaterialController::class, 'storeRawMaterial'])->name('raw-materials.store')->middleware('permission:Raw Materials|Purchase');
    Route::match(['get', 'delete'], '/raw-materials/delete/{id}', [RawMaterialController::class, 'deleteRawMaterial'])->name('raw-materials.delete')->middleware('permission:Raw Materials|Purchase');
    Route::post('/raw-materials/purchase/store', [RawMaterialController::class, 'storePurchase'])->name('raw-materials.purchase.store')->middleware('permission:Raw Materials|Purchase');
    Route::match(['get', 'delete'], '/raw-materials/purchase/delete/{id}', [RawMaterialController::class, 'deletePurchase'])->name('raw-materials.purchase.delete')->middleware('permission:Raw Materials|Purchase');
    Route::get('/raw-materials/stock', [RawMaterialController::class, 'getStock'])->name('raw-materials.stock')->middleware('permission:Raw Materials|Purchase');
    Route::get('/raw-materials/usage-detail/{id}', [RawMaterialController::class, 'getUsageDetail'])->name('raw-materials.usage-detail.direct')->middleware('permission:Raw Materials|Purchase');

    // Search Products
    Route::get('/search-products', [ProductController::class, 'searchProducts'])->name('search-products');

    // Purchase Invoice
    Route::get('/purchase/{id}/invoice', [PurchaseController::class, 'Invoice'])->name('purchase.invoice');
    Route::get('/purchasereturn/{id}/invoice', [PurchaseController::class, 'ReturnInvoice'])->name('purchasereturn.invoice');

    // Inward Gatepass Routes
    Route::get('/InwardGatepass', [InwardgatepassController::class, 'index'])->name('InwardGatepass.home')->middleware('permission:List Inwards');
    Route::get('/add/InwardGatepass', [InwardgatepassController::class, 'create'])->name('add_inwardgatepass')->middleware('permission:Create Inward Gatepass');
    Route::post('/InwardGatepass/store', [InwardgatepassController::class, 'store'])->name('store.InwardGatepass');
    Route::get('/InwardGatepas/{id}', [InwardgatepassController::class, 'show'])->name('InwardGatepass.show');
    Route::get('/InwardGatepasinv/{id}', [InwardgatepassController::class, 'show_inv'])->name('InwardGatepass.inv');
    Route::delete('/inward-gatepass/{id}', [InwardgatepassController::class, 'destroy'])->name('InwardGatepass.destroy');

    // Notifications
    Route::get('/notifications/inward/check', [InwardgatepassController::class, 'checkNewInwards'])->name('notifications.check_inwards');
    Route::post('/notifications/inward/mark-notified', [InwardgatepassController::class, 'markInwardsNotified'])->name('notifications.mark_inwards');

    // Stock Transfer Notifications
    Route::get('/notifications/transfer/check', [StockTransferController::class, 'checkNewTransfers'])->name('notifications.check_transfers');
    Route::post('/notifications/transfer/mark-notified', [StockTransferController::class, 'markTransfersNotified'])->name('notifications.mark_transfers');

    Route::get('inward-gatepass/{id}/add-details', [InwardgatepassController::class, 'addDetails'])
        ->name('InwardGatepass.addDetails');

    Route::get('/search-product-by-barcode', [InwardgatepassController::class, 'searchByBarcode'])
        ->name('search-product-by-barcode');

    Route::post('inward-gatepass/{id}/store-details', [InwardgatepassController::class, 'storeDetails'])
        ->name('InwardGatepass.storeDetails');

    Route::get('/InwardGatepass/{id}/edit', [InwardgatepassController::class, 'edit'])->name('InwardGatepass.edit');
    Route::put('/InwardGatepass/{id}', [InwardgatepassController::class, 'update'])->name('InwardGatepass.update');
    Route::get('/inward-gatepass/{id}/pdf', [InwardgatepassController::class, 'pdf'])->name('InwardGatepass.pdf');
    Route::delete('/InwardGatepass/{id}', [InwardgatepassController::class, 'destroy'])->name('InwardGatepass.destroy');

    // Inward Bill Routes
    Route::get('inward-gatepass/{id}/add-bill', [PurchaseController::class, 'addBill'])->name('add_bill');
    Route::post('inward-gatepass/{id}/store-bill', [PurchaseController::class, 'store_inwardbill'])->name('store.bill');
    Route::get('inward-gatepass/{id}/edit-bill', [PurchaseController::class, 'editBill'])->name('edit_bill');
    Route::post('inward-gatepass/{id}/update-bill', [PurchaseController::class, 'update_inwardbill'])->name('update.bill');

    // Purchase Return Routes
    Route::get('purchase/return', [PurchaseController::class, 'purchaseReturnIndex'])->name('purchase.return.index')->middleware('permission:Purchase Return');
    Route::get('purchase/return/{id}', [PurchaseController::class, 'showReturnForm'])->name('purchase.return.show');
    Route::post('purchase/return/store', [PurchaseController::class, 'storeReturn'])->name('purchase.return.store');

    // Sale Routes
    Route::get('sale', [SaleController::class, 'index'])->name('sale.index')->middleware('permission:Sales');
    Route::get('sale/pos', [SaleController::class, 'posSale'])->name('sale.pos')->middleware('permission:Sales|Sale Screen');
    Route::get('sale-screen', [SaleController::class, 'saleScreen'])->name('sale.screen')->middleware('permission:Sales|Sale Screen');
    Route::get('sale/create', [SaleController::class, 'saleScreen'])->name('sale.add')->middleware('permission:Sales|Sale Screen');
    Route::post('/sale/quick-product', [SaleController::class, 'quickStoreProduct'])->name('sale.quick_product');
    Route::get('/search-product-name', [SaleController::class, 'searchpname'])->name('search-product-name');
    Route::get('/pos/products', [SaleController::class, 'getPosProducts'])->name('pos.products');
    Route::get('/pos/product-variants/{id}', [SaleController::class, 'getProductVariants'])->name('pos.product.variants');
    Route::get('/pos/scan-barcode', [SaleController::class, 'scanBarcode'])->name('pos.scan.barcode');
    Route::get('/pos/recent-products', [SaleController::class, 'getRecentProducts'])->name('pos.recent.products');
    Route::get('/pos/invoice-search', [SaleController::class, 'searchInvoiceForExchange'])->name('pos.invoice.search');
    Route::get('/pos/table-active-sale/{id}', [SaleController::class, 'getActiveSaleForTable'])->name('pos.table.active');
    Route::get('/pos/print-bill/{id}', [SaleController::class, 'printTableBill'])->name('pos.print.bill');
    Route::post('/sales/store', [SaleController::class, 'store'])->name('sales.store');
    Route::get('/sales/{id}/return', [SaleController::class, 'saleretun'])->name('sales.return.create');
    Route::post('/sales-return/store', [SaleController::class, 'storeSaleReturn'])->name('sales.return.store');
    Route::get('/sale-returns', [SaleController::class, 'salereturnview'])->name('sale.returns.index')->middleware('permission:Sale Return');
    Route::get('/sales/{id}/invoice', [SaleController::class, 'saleinvoice'])->name('sales.invoice');
    Route::get('/sales/{id}/details', [SaleController::class, 'saleDetails'])->name('sales.details');
    Route::get('/sales/{id}/edit', [SaleController::class, 'saleedit'])->name('sales.edit');
    Route::match(['put', 'post'], '/sales/{id}', [SaleController::class, 'updatesale'])->name('sales.update');
    Route::get('/sales/{id}/dc', [SaleController::class, 'saledc'])->name('sales.dc');
    Route::get('/sales/{id}/recepit', [SaleController::class, 'salerecepit'])->name('sales.recepit');
    Route::get('/sale-return/invoice/{id}', [SaleController::class, 'retrninvoice'])->name('saleReturn.invoice');

    // Booking Routes
    Route::get('bookings', [ProductBookingController::class, 'index'])->name('bookings.index')->middleware('permission:Bookings');
    Route::get('bookings/create', [ProductBookingController::class, 'create'])->name('bookings.create');
    Route::post('bookings/store', [ProductBookingController::class, 'store'])->name('bookings.store');
    Route::get('booking/receipt/{id}', [ProductBookingController::class, 'receipt'])->name('booking.receipt');
    Route::get('/sales/from-booking/{id}', [SaleController::class, 'convertFromBooking'])->name('sales.from.booking');
    Route::delete('bookings/{id}', [ProductBookingController::class, 'destroy'])->name('bookings.destroy');

    // Stock Transfer Routes
    Route::get('/warehouse-stock-quantity', [StockTransferController::class, 'getStockQuantity'])->name('warehouse.stock.quantity');
    Route::get('/warehouse-stock-receipt/{id?}', [StockTransferController::class, 'receipt'])->name('stock_transfers.receipt');
    Route::get('/warehouse-stock-receipt-legacy/{id?}', [StockTransferController::class, 'receipt'])->name('recipt.warehouse');
    Route::post('/stock_transfers/{id}/accept', [StockTransferController::class, 'accept'])->name('stock_transfers.accept');
    Route::post('/stock_transfers/{id}/reject', [StockTransferController::class, 'reject'])->name('stock_transfers.reject');

    // Narrations
    Route::get('/get-customers-by-type', [CustomerController::class, 'getByType']);
    Route::resource('warehouse_stocks', WarehouseStockController::class)->middleware('permission:Warehouse Stock');
    Route::resource('stock_transfers', StockTransferController::class)->middleware('permission:Stock Transfer');

    Route::resource('narrations', NarrationController::class)->only(['index', 'store', 'destroy'])->middleware('permission:Narrations');
    Route::get('vouchers/{type}', [VoucherController::class, 'index'])->name('vouchers.index');
    Route::post('vouchers/store', [VoucherController::class, 'store'])->name('vouchers.store');
    Route::get('/get-vendor-balance/{id}', [VendorController::class, 'getVendorBalance']);

    // Reporting Routes
    Route::get('/report/item-stock', [ReportingController::class, 'item_stock_report'])->name('report.item_stock')->middleware('permission:Item Stock Report');
    Route::post('/report/item-stock-fetch', [ReportingController::class, 'fetchItemStock'])->name('report.item_stock.fetch');
    Route::get('/report/item-stock/closing-print', [ReportingController::class, 'printClosing'])->name('report.item_stock.closing_print');
    Route::post('/report/variant-stock-fetch', [ReportingController::class, 'fetchVariantStock'])->name('report.variant_stock.fetch');

    Route::get('/report/branch-stock', [ReportingController::class, 'branch_stock_report'])->name('report.branch_stock')->middleware('permission:Branch Stock Report');
    Route::get('/report/branch-stock-fetch', [ReportingController::class, 'fetchBranchStockReport'])->name('report.branch_stock.fetch')->middleware('permission:Branch Stock Report');

    Route::get('report/purchase', [ReportingController::class, 'purchase_report'])->name('report.purchase')->middleware('permission:Purchase Report');
    Route::get('report/production', [ReportingController::class, 'production_report'])->name('report.production')->middleware('permission:Production Report|Production|Purchase|Sale Report|System Reports');
    Route::post('report/production/fetch', [ReportingController::class, 'fetchProductionReport'])->name('report.production.fetch')->middleware('permission:Production Report|Production|Purchase|Sale Report|System Reports');
    Route::post('report/purchase/fetch', [ReportingController::class, 'fetchPurchaseReport'])->name('report.purchase.fetch')->middleware('permission:Purchase Report');

    Route::get('report/sale', [ReportingController::class, 'sale_report'])->name('report.sale')->middleware('permission:Sale Report');
    Route::get('report/sale/fetch', [ReportingController::class, 'fetchsaleReport'])->name('report.sale.fetch')->middleware('permission:Sale Report');

    Route::get('report/sale-closing', [ReportingController::class, 'sale_closing_report'])->name('report.sale_closing')->middleware('permission:Sale Report|Daily Sale Closing Report');
    Route::get('report/sale-closing/fetch', [ReportingController::class, 'fetchSaleClosingReport'])->name('report.sale_closing.fetch')->middleware('permission:Sale Report|Daily Sale Closing Report');
    Route::get('report/sale-closing/print', [ReportingController::class, 'printSaleClosingReport'])->name('report.sale_closing.print')->middleware('permission:Sale Report|Daily Sale Closing Report|Print Sale Closing');

    Route::get('report/sale/category', [ReportingController::class, 'sale_report_category'])->name('report.sale.category')->middleware('permission:Sale Report|Category Sale Report');
    Route::get('report/sale/category/fetch', [ReportingController::class, 'fetchsalecategoryReport'])->name('report.sale.category.fetch')->middleware('permission:Sale Report|Category Sale Report');

    Route::get('report/customer/ledger', [ReportingController::class, 'customer_ledger_report'])->name('report.customer.ledger')->middleware('permission:Customer Ledger');
    Route::get('report/customer-ledger/fetch', [ReportingController::class, 'fetch_customer_ledger'])->name('report.customer.ledger.fetch')->middleware('permission:Customer Ledger');

    Route::get('report/vendor/ledger', [ReportingController::class, 'vendor_ledger_report'])->name('report.vendor.ledger')->middleware('permission:Vendor Ledger');
    Route::get('report/vendor-ledger/fetch', [ReportingController::class, 'fetch_vendor_ledger'])->name('report.vendor.ledger.fetch')->middleware('permission:Vendor Ledger');
    Route::get('report/vendor-ledger/pdf', [ReportingController::class, 'vendor_ledger_pdf'])->name('report.vendor.ledger.pdf')->middleware('permission:Vendor Ledger');

    Route::get('report/expense/vocher', [ReportingController::class, 'expense_vocher'])->name('expense.vocher')->middleware('permission:Expense Report|Expense Audit Report|Expense Voucher');
    Route::get('/expense-voucher/ajax', [ReportingController::class, 'expenseVoucherAjax'])->name('expense.voucher.ajax')->middleware('permission:Expense Report|Expense Audit Report|Expense Voucher');

    // Profit & Loss Report Routes
    Route::get('report/profit-loss', [ReportingController::class, 'profit_loss_report'])->name('report.profit_loss')->middleware('permission:Sale Report|Profit Loss Report');
    Route::post('report/profit-loss/fetch', [ReportingController::class, 'fetchProfitLossReport'])->name('report.profit_loss.fetch')->middleware('permission:Sale Report|Profit Loss Report');

    // Balance Sheet Report Routes
    Route::get('report/balance-sheet', [ReportingController::class, 'balance_sheet_report'])->name('report.balance_sheet')->middleware('permission:Balance Sheet Report|Sale Report|Profit Loss Report|System Reports');
    Route::post('report/balance-sheet/fetch', [ReportingController::class, 'fetchBalanceSheetReport'])->name('report.balance_sheet.fetch')->middleware('permission:Balance Sheet Report|Sale Report|Profit Loss Report|System Reports');
    Route::get('report/balance-sheet/print', [ReportingController::class, 'printBalanceSheetReport'])->name('report.balance_sheet.print')->middleware('permission:Print Balance Sheet|Balance Sheet Report|Sale Report|Profit Loss Report|System Reports');

    // Chart of Accounts
    Route::get('/view_all', [AccountsHeadController::class, 'index'])->name('view_all')->middleware('permission:Char Of Accounts');
    Route::delete('/coa/account/{id}', [AccountsHeadController::class, 'destroy'])->name('coa.account.delete')->middleware('permission:Char Of Accounts|Delete Account');

    Route::get('/getPartyList', [NarrationController::class, 'getPartyList'])->name('party.list');
    Route::get('/get-customer/{id}', [NarrationController::class, 'getCustomerData'])->name('customers.show');
    Route::get('/get-accounts-by-head/{headId}', [NarrationController::class, 'getAccountsByHead']);

    // Receipt Vouchers
    Route::get('/all-recepit-vochers', [VoucherController::class, 'all_recepit_vochers'])->name('all-recepit-vochers')->middleware('permission:Receipts Voucher');
    Route::get('/recepit-vochers', [VoucherController::class, 'recepit_vochers'])->name('recepit-vochers')->middleware('permission:Receipts Voucher|Create Receipt Voucher');
    Route::post('/recepit/vochers/store', [VoucherController::class, 'store_rec_vochers'])->name('recepit.vochers.store')->middleware('permission:Receipts Voucher|Create Receipt Voucher');
    Route::get('/receipt-voucher/print/{id}', [VoucherController::class, 'print'])->name('receiptVoucher.print')->middleware('permission:Receipts Voucher|Print Receipt Voucher');

    // Payment Vouchers
    Route::get('/Payment-vochers', [VoucherController::class, 'Payment_vochers'])->name('Payment-vochers')->middleware('permission:Payment Voucher|Create Payment Voucher');
    Route::post('/Payment/vochers/store', [VoucherController::class, 'store_Pay_vochers'])->name('Payment.vochers.store')->middleware('permission:Payment Voucher|Create Payment Voucher');
    Route::get('/all-Payment-vochers', [VoucherController::class, 'all_Payment_vochers'])->name('all-Payment-vochers')->middleware('permission:Payment Voucher');
    Route::get('/Payment-voucher/print/{id}', [VoucherController::class, 'Paymentprint'])->name('PaymentVoucher.print')->middleware('permission:Payment Voucher|Print Payment Voucher');

    // Expense Vouchers
    Route::get('/expense-vochers', [VoucherController::class, 'expense_vochers'])->name('expense-vochers')->middleware('permission:Expense Voucher|Create Expense Voucher');
    Route::post('/expense/vochers/store', [VoucherController::class, 'store_expense_vochers'])->name('expense.vochers.store')->middleware('permission:Expense Voucher|Create Expense Voucher');
    Route::get('/all-expense-vochers', [VoucherController::class, 'all_expense_vochers'])->name('all-expense-vochers')->middleware('permission:Expense Voucher');
    Route::get('/expense-voucher/print/{id}', [VoucherController::class, 'expenseprint'])->name('expenseVoucher.print')->middleware('permission:Expense Voucher|Print Expense Voucher');

    Route::get('cashbook', [ReportingController::class, 'cashbook'])->name('cashbook')->middleware('permission:Cashbook|View All Cashbook|View Own Cashbook');

    // COA Prefix Routes
    Route::prefix('coa')->group(function () {
        Route::get('/', [AccountsHeadController::class, 'index'])->name('coa.index');
        Route::get('/sync-sales', [AccountsHeadController::class, 'syncSalesPayments'])->name('coa.sync_sales');
        Route::post('/head', [AccountsHeadController::class, 'storeHead'])->name('coa.head.store');
        Route::post('/account', [AccountsHeadController::class, 'storeAccount'])->name('coa.account.store');
    });

    // Manual Entry Routes
    Route::post('/subcategory/manual', [ManuallController::class, 'subcategory'])->name('manual.subcategory');
    Route::post('/Brand/manual', [ManuallController::class, 'brand'])->name('manual.Brand');
    Route::post('/category/manual', [ManuallController::class, 'category'])->name('manual.category');
    Route::post('/Unit/manual', [ManuallController::class, 'unit'])->name('manual.Unit');

    // Stock Adjustment Routes
    Route::get('/stock-adjustment', [StockAdjustmentController::class, 'index'])->name('stock-adjustment.index')->middleware('permission:Stock Adjustment');
    Route::get('/stock-adjustment/create', [StockAdjustmentController::class, 'create'])->name('stock-adjustment.create')->middleware('permission:Stock Adjustment');
    Route::post('/stock-adjustment/store', [StockAdjustmentController::class, 'store'])->name('stock-adjustment.store')->middleware('permission:Stock Adjustment');
    Route::get('/stock-adjustment/{id}', [StockAdjustmentController::class, 'show'])->name('stock-adjustment.show')->middleware('permission:Stock Adjustment');
    Route::get('/stock-adjustment/{id}/edit', [StockAdjustmentController::class, 'edit'])->name('stock-adjustment.edit')->middleware('permission:Stock Adjustment|Edit Stock Adjustment');
    Route::match(['put', 'post'], '/stock-adjustment/{id}', [StockAdjustmentController::class, 'update'])->name('stock-adjustment.update')->middleware('permission:Stock Adjustment|Edit Stock Adjustment');
    Route::delete('/stock-adjustment/{id}', [StockAdjustmentController::class, 'destroy'])->name('stock-adjustment.destroy')->middleware('permission:Stock Adjustment');
    Route::get('/stock-adjustment-report', [StockAdjustmentController::class, 'report'])->name('stock-adjustment.report')->middleware('permission:Stock Adjustment');

    // Raw Material Routes (new grouped)
    Route::prefix('raw-materials')->middleware('permission:Raw Materials|Purchase')->group(function () {
        Route::get('/', [RawMaterialController::class, 'index'])->name('raw-materials.index');
        Route::post('/store', [RawMaterialController::class, 'storeRawMaterial'])->name('raw-materials.store');
        Route::match(['get', 'delete'], '/delete/{id}', [RawMaterialController::class, 'deleteRawMaterial'])->name('raw-materials.delete');
        Route::post('/purchase/store', [RawMaterialController::class, 'storePurchase'])->name('raw-materials.purchase.store');
        Route::match(['get', 'delete'], '/purchase/delete/{id}', [RawMaterialController::class, 'deletePurchase'])->name('raw-materials.purchase.delete');
        Route::get('/purchase/print/{id}', [RawMaterialController::class, 'printPurchase'])->name('raw-materials.purchase.print');
        Route::post('/purchase/return/store', [RawMaterialController::class, 'storePurchaseReturn'])->name('raw-materials.purchase.return.store');
        Route::match(['get', 'delete'], '/purchase/return/delete/{id}', [RawMaterialController::class, 'deletePurchaseReturn'])->name('raw-materials.purchase.return.delete');
        Route::get('/purchase/return/print/{id}', [RawMaterialController::class, 'printPurchaseReturn'])->name('raw-materials.purchase.return.print');
        Route::get('/purchase/{id}/return-data', [RawMaterialController::class, 'getPurchaseReturnData'])->name('raw-materials.purchase.return-data');
        Route::get('/stock/get', [RawMaterialController::class, 'getStock'])->name('raw-materials.stock.get');
        Route::get('/usage-detail/{id}', [RawMaterialController::class, 'getUsageDetail'])->name('raw-materials.usage-detail');
    });

    // ==========================================
    // SUPER ADMIN / COMPANY DISTRIBUTOR ROUTES
    // ==========================================
    Route::prefix('admin/distributors')->name('admin.distributors.')->middleware('permission:Distributors|Distributor Management|Distributor Orders|Distributor Sales|Distributor Custody Stocks|Distributor Ledger|Distributor Secondary Sales|Distributor Customers|Distributor Payments|Distributor Claims')->group(function () {
        Route::get('/', [DistributorAdminController::class, 'index'])->name('index')->middleware('permission:Distributors|Distributor Management');
        Route::post('/store', [DistributorAdminController::class, 'store'])->name('store')->middleware('permission:Create Distributor');
        Route::post('/update/{id}', [DistributorAdminController::class, 'update'])->name('update')->middleware('permission:Edit Distributor');
        Route::get('/orders', [DistributorAdminController::class, 'orders'])->name('orders')->middleware('permission:Distributor Orders');
        Route::get('/sales', [DistributorAdminController::class, 'salesList'])->name('sales')->middleware('permission:Distributor Sales');
        Route::get('/sales/{id}', [DistributorAdminController::class, 'saleInvoice'])->name('sales.invoice');
        Route::get('/direct-sale', [DistributorAdminController::class, 'createDirectSale'])->name('direct_sale')->middleware('permission:Create Distributor Direct Sale');
        Route::post('/direct-sale/store', [DistributorAdminController::class, 'storeDirectSale'])->name('direct_sale.store')->middleware('permission:Create Distributor Direct Sale');
        Route::get('/orders/{id}', [DistributorAdminController::class, 'showOrder'])->name('orders.show')->middleware('permission:Distributor Orders');
        Route::post('/orders/{id}/dispatch', [DistributorAdminController::class, 'dispatchOrder'])->name('orders.dispatch')->middleware('permission:Dispatch Distributor Order');
        Route::get('/stocks', [DistributorAdminController::class, 'stocks'])->name('stocks')->middleware('permission:Distributor Custody Stocks');
        Route::get('/opening-stock', [DistributorAdminController::class, 'createOpeningStock'])->name('opening_stock')->middleware('permission:Distributor Custody Stocks');
        Route::post('/opening-stock/store', [DistributorAdminController::class, 'storeOpeningStock'])->name('opening_stock.store')->middleware('permission:Distributor Custody Stocks');
        Route::get('/ledger/{id}', [DistributorAdminController::class, 'ledger'])->name('ledger')->middleware('permission:Distributor Ledger');
        Route::post('/payment/{id}', [DistributorAdminController::class, 'storePayment'])->name('payment.store');
        Route::get('/secondary-sales', [DistributorAdminController::class, 'secondarySales'])->name('secondary_sales')->middleware('permission:Distributor Secondary Sales');
        
        // Distributor Customers (Retailers) & Ledgers
        Route::get('/customers', [DistributorAdminController::class, 'customers'])->name('customers')->middleware('permission:Distributor Customers');
        Route::post('/customers/store', [DistributorAdminController::class, 'storeCustomer'])->name('customers.store')->middleware('permission:Create Distributor Customer');
        Route::post('/customers/{id}/update', [DistributorAdminController::class, 'updateCustomer'])->name('customers.update')->middleware('permission:Edit Distributor Customer');
        Route::delete('/customers/{id}/delete', [DistributorAdminController::class, 'destroyCustomer'])->name('customers.destroy')->middleware('permission:Delete Distributor Customer');
        Route::get('/customers/{id}/ledger', [DistributorAdminController::class, 'customerLedger'])->name('customers.ledger')->middleware('permission:Distributor Customer Ledger');
        Route::post('/customers/{id}/payment', [DistributorAdminController::class, 'storeCustomerPayment'])->name('customers.payment.store');

        // Payments / Collections & Approvals
        Route::get('/payments', [DistributorAdminController::class, 'payments'])->name('payments')->middleware('permission:Distributor Payments');
        Route::post('/payments/store', [DistributorAdminController::class, 'storeAdminPayment'])->name('payments.store')->middleware('permission:Create Distributor Payment');
        Route::post('/payments/{id}/update', [DistributorAdminController::class, 'updatePayment'])->name('payments.update')->middleware('permission:Edit Distributor Payment');
        Route::delete('/payments/{id}/delete', [DistributorAdminController::class, 'destroyPayment'])->name('payments.destroy')->middleware('permission:Delete Distributor Payment');
        Route::post('/payments/{id}/approve', [DistributorAdminController::class, 'approvePayment'])->name('payments.approve')->middleware('permission:Approve Distributor Payment');
        Route::post('/payments/{id}/reject', [DistributorAdminController::class, 'rejectPayment'])->name('payments.reject')->middleware('permission:Reject Distributor Payment');

        // Claims / Secondary Discounts / Damage Stock / Freight
        Route::get('/claims', [DistributorAdminController::class, 'claims'])->name('claims')->middleware('permission:Distributor Claims');
        Route::post('/claims/store', [DistributorAdminController::class, 'storeClaim'])->name('claims.store')->middleware('permission:Create Distributor Claim');
        Route::post('/claims/{id}/approve', [DistributorAdminController::class, 'approveClaim'])->name('claims.approve')->middleware('permission:Approve Distributor Claim');
        Route::post('/claims/{id}/reject', [DistributorAdminController::class, 'rejectClaim'])->name('claims.reject')->middleware('permission:Reject Distributor Claim');
        Route::delete('/claims/{id}/delete', [DistributorAdminController::class, 'destroyClaim'])->name('claims.destroy')->middleware('permission:Delete Distributor Claim');
    });

    // ==========================================
    // DISTRIBUTOR / ORDER BOOKER DEDICATED PORTAL
    // ==========================================
    Route::prefix('distributor-portal')->name('distributor.')->middleware('permission:Distributor Portal')->group(function () {
        Route::get('/dashboard', [DistributorPortalController::class, 'dashboard'])->name('dashboard');
        
        // Stock
        Route::get('/stock', [DistributorPortalController::class, 'myStock'])->name('stock.index');
        
        // Demand Orders (to Super Admin)
        Route::get('/orders', [DistributorPortalController::class, 'myOrders'])->name('orders.index');
        Route::get('/orders/create', [DistributorPortalController::class, 'createOrder'])->name('orders.create');
        Route::post('/orders/store', [DistributorPortalController::class, 'storeOrder'])->name('orders.store');
        Route::get('/orders/{id}', [DistributorPortalController::class, 'showOrder'])->name('orders.show');
        
        // Customers (Retail Shops)
        Route::get('/customers', [DistributorPortalController::class, 'myCustomers'])->name('customers.index');
        Route::post('/customers/store', [DistributorPortalController::class, 'storeCustomer'])->name('customers.store');
        Route::post('/customers/{id}/update', [DistributorPortalController::class, 'updateCustomer'])->name('customers.update');
        Route::get('/customers/{id}/ledger', [DistributorPortalController::class, 'customerLedger'])->name('customers.ledger');
        Route::post('/customers/{id}/payment', [DistributorPortalController::class, 'storeCustomerPayment'])->name('customers.payment.store');

        // Secondary Sales (to Retail Shops)
        Route::get('/sales', [DistributorPortalController::class, 'salesList'])->name('sales.index');
        Route::get('/sales/create', [DistributorPortalController::class, 'createSale'])->name('sales.create');
        Route::post('/sales/store', [DistributorPortalController::class, 'storeSale'])->name('sales.store');
        Route::get('/sales/receipt/{id}', [DistributorPortalController::class, 'saleReceipt'])->name('sales.receipt');

        // Accounts (Chart of Accounts for Distributor)
        Route::get('/accounts', [DistributorPortalController::class, 'accountsList'])->name('accounts.index');
        Route::post('/accounts/store', [DistributorPortalController::class, 'storeAccount'])->name('accounts.store');
        Route::post('/accounts/store-quick', [DistributorPortalController::class, 'storeQuickAccount'])->name('accounts.store_quick');
        Route::delete('/accounts/{id}/delete', [DistributorPortalController::class, 'destroyAccount'])->name('accounts.destroy');
        
        // Company Ledger
        Route::get('/company-ledger', [DistributorPortalController::class, 'companyLedger'])->name('company_ledger.index');

        // Payments to Company (Submit & Track)
        Route::get('/payments', [DistributorPortalController::class, 'payments'])->name('payments.index');
        Route::post('/payments/store', [DistributorPortalController::class, 'storePayment'])->name('payments.store');
        Route::post('/payments/{id}/update', [DistributorPortalController::class, 'updatePayment'])->name('payments.update');
        Route::delete('/payments/{id}/delete', [DistributorPortalController::class, 'destroyPayment'])->name('payments.destroy');

        // Claims to Company (Trade Schemes, Damage Stock, Freight)
        Route::get('/claims', [DistributorPortalController::class, 'claims'])->name('claims.index');
        Route::post('/claims/store', [DistributorPortalController::class, 'storeClaim'])->name('claims.store');

        // Order Bookers Management (Distributor manages his bookers)
        Route::get('/bookers', [DistributorPortalController::class, 'bookersList'])->name('bookers.index');
        Route::post('/bookers/store', [DistributorPortalController::class, 'storeBooker'])->name('bookers.store');
        Route::delete('/bookers/{id}/delete', [DistributorPortalController::class, 'destroyBooker'])->name('bookers.destroy');

        // Order Booker Interface (Booking shop orders with live previous balance)
        Route::get('/booker/create', [DistributorPortalController::class, 'createShopBooking'])->name('booker.create');
        Route::get('/booker/shop-info/{id}', [DistributorPortalController::class, 'getShopCreditInfo'])->name('booker.shop_info');
        Route::post('/booker/store', [DistributorPortalController::class, 'storeShopBooking'])->name('booker.store');
        Route::get('/booker/my-bookings', [DistributorPortalController::class, 'myShopBookings'])->name('booker.my_bookings');
        Route::get('/booker/booking/{id}', [DistributorPortalController::class, 'showShopBookingReceipt'])->name('booker.receipt');

        // Distributor View & Deliver of Shop Bookings
        Route::get('/shop-bookings', [DistributorPortalController::class, 'shopBookingsList'])->name('shop_bookings.index');
        Route::get('/shop-bookings/{id}', [DistributorPortalController::class, 'showShopBookingDetails'])->name('shop_bookings.show');
        Route::post('/shop-bookings/{id}/deliver', [DistributorPortalController::class, 'deliverShopBooking'])->name('shop_bookings.deliver');
        Route::post('/shop-bookings/{id}/cancel', [DistributorPortalController::class, 'cancelShopBooking'])->name('shop_bookings.cancel');
    });

    // ==========================================
    // ASSETS MANAGEMENT (FURNITURE & MACHINERY)
    // ==========================================
    Route::prefix('fixed-assets')->name('assets.')->middleware('permission:Assets|View Assets|Create Asset|Edit Asset|Delete Asset|Asset Maintenance|Asset Transfer|Dispose Asset|Print Asset Barcode|Asset Report|Asset Categories')->group(function () {
        Route::get('/', [AssetController::class, 'index'])->name('index')->middleware('permission:Assets|View Assets');
        Route::get('/create', [AssetController::class, 'create'])->name('create')->middleware('permission:Create Asset');
        Route::post('/store', [AssetController::class, 'store'])->name('store')->middleware('permission:Create Asset');
        Route::get('/report', [AssetController::class, 'report'])->name('report')->middleware('permission:Asset Report');
        Route::get('/get-next-code', [AssetController::class, 'getNextCode'])->name('get_next_code');
        Route::get('/categories', [AssetCategoryController::class, 'index'])->name('categories.index')->middleware('permission:Asset Categories');
        Route::post('/categories/store', [AssetCategoryController::class, 'store'])->name('categories.store')->middleware('permission:Create Asset Category');
        Route::delete('/categories/{id}/delete', [AssetCategoryController::class, 'destroy'])->name('categories.destroy')->middleware('permission:Delete Asset Category');
        Route::get('/{id}', [AssetController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [AssetController::class, 'edit'])->name('edit')->middleware('permission:Edit Asset');
        Route::put('/{id}/update', [AssetController::class, 'update'])->name('update')->middleware('permission:Edit Asset');
        Route::delete('/{id}/delete', [AssetController::class, 'destroy'])->name('destroy')->middleware('permission:Delete Asset');
        Route::get('/{id}/barcode', [AssetController::class, 'barcode'])->name('barcode')->middleware('permission:Print Asset Barcode');
        Route::post('/{id}/maintenance', [AssetController::class, 'storeMaintenance'])->name('maintenance.store')->middleware('permission:Asset Maintenance');
        Route::put('/maintenance/{id}/update', [AssetController::class, 'updateMaintenance'])->name('maintenance.update')->middleware('permission:Asset Maintenance');
        Route::delete('/maintenance/{id}/delete', [AssetController::class, 'destroyMaintenance'])->name('maintenance.destroy')->middleware('permission:Asset Maintenance');
        Route::post('/{id}/transfer', [AssetController::class, 'storeTransfer'])->name('transfer.store')->middleware('permission:Asset Transfer');
        Route::post('/{id}/dispose', [AssetController::class, 'dispose'])->name('dispose')->middleware('permission:Dispose Asset');
    });

});

require __DIR__ . '/auth.php';
