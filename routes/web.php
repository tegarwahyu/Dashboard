<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\OutboxController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\OutletController;
use App\Http\Controllers\AktualController;
use App\Http\Controllers\UserControllers;
use App\Http\Controllers\TargetOutletController;
use App\Http\Controllers\SalarOutletController;
use App\Http\Controllers\BranchController;
// use App\Http\Controllers\;
use App\Http\Controllers\KompetitorController;
use App\Http\Controllers\MenuTemplateControllers;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Route::get('/dashboard-data', [DashboardController::class, 'dashboardData']);

// Route::get('/dashboard', [DashboardController::class, 'indexAccounting'])->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');
// get data outlet
Route::get('/dashboard/general-data', [DashboardController::class, 'getGeneralData'])->middleware(['auth', 'verified']);
// route marketing
// Route::get('/dashboard-marketing/program-sort-by-brand', [DashboardController::class, 'getProgramBaseBrandData'])->middleware(['auth', 'verified']);
Route::get('/get-brand-list', [DashboardController::class, 'getBrandList'])->middleware(['auth', 'verified']);
Route::get('/get-promotions-by-brand/{nama_brand}', [DashboardController::class, 'getPromotionsByBrand'])->middleware(['auth', 'verified']);
Route::get('/dashboard/program-promo', [DashboardController::class, 'getProgramPromolData'])->middleware(['auth', 'verified']);
Route::get('/dashboard/get-program-dashboard2', [DashboardController::class, 'getProgramData2'])->middleware(['auth', 'verified']);
Route::get('/dashboard/get-program-dashboard', [DashboardController::class, 'getProgramData'])->middleware(['auth', 'verified']);
Route::get('/dashboard/get-promo-actual', [DashboardController::class, 'getPromoActual'])->middleware(['auth', 'verified']);
Route::get('/dashboard/get-promo-actual2', [DashboardController::class, 'getPromoActual2'])->middleware(['auth', 'verified']);
Route::get('/dashboard/get-menu-kode', [DashboardController::class, 'getMenuCode'])->middleware(['auth', 'verified']);
Route::get('/dashboard/get-menu-category', [DashboardController::class, 'getMenuCategory'])->name('promo.menu.category');
// dropdown menu category detail (tergantung brand + category)
Route::get('/dashboard/get-menu-category-detail', [DashboardController::class, 'getMenuCategoryDetail'])->name('promo.menu.categoryDetail');
Route::get('/dashboard/get-branch-list', [DashboardController::class, 'getBranchDashboard'])->middleware(['auth', 'verified']);
Route::get('/dashboard/menu-report', [DashboardController::class, 'getMenuReport'])->middleware(['auth', 'verified']);

// route akunting 
Route::get('/dashboard-accounting/parameter', [DashboardController::class, 'getAccountingParameter'])->middleware(['auth', 'verified']);
Route::get('/dashboard-accounting/parameter-brand', [DashboardController::class, 'getAccountingParameterBrand'])->middleware(['auth', 'verified']);
Route::get('/dashboard-accounting/analisa-item', [DashboardController::class, 'getAnalisaItem'])->middleware(['auth', 'verified']);
// rekap daily sales
Route::get('/dashboard-accounting/data', [DashboardController::class, 'getAccountingData'])->middleware(['auth', 'verified']);
// summary acounting 
Route::get('/dashboard/summary-sales-report', [DashboardController::class, 'getSummarySalesReport'])->middleware(['auth', 'verified']);
Route::get('/dashboard/rank', [DashboardController::class, 'getRangking'])->middleware(['auth', 'verified']);
Route::get('/dashboard/compare', [DashboardController::class, 'getCompare'])->middleware(['auth', 'verified']);
Route::get('/dashboard/summary', [DashboardController::class, 'getGeneralSummary'])->middleware(['auth', 'verified']);
Route::get('/dashboard/getDataDasboard', [DashboardController::class, 'getDataDashboard'])->middleware(['auth', 'verified'])->name('getDataDashboard');
Route::get('/get-brands', [DashboardController::class, 'getBrands'])->middleware(['auth', 'verified']);
Route::get('/get-outlets', [DashboardController::class, 'getOutlets'])->middleware(['auth', 'verified']);
// old
Route::get('/komparasi-ajax', [DashboardController::class, 'getAjaxDataComparasi']);
Route::get('/get-outlet-promosi', [DashboardController::class, 'filterOutletPromosi'])->middleware(['auth', 'verified'])->name('getOutletPromosi');

Route::get('/grafik-perbandingan', [DashboardController::class, 'index']);
Route::get('/get-outlet-by-brand/{brandId}', [DashboardController::class, 'getOutletByBrand']);
Route::get('/get-data-aktual', [DashboardController::class, 'getDataAktual']);
Route::get('/get-sales-per-outlet', [DashboardController::class, 'getSalesPerOutlet']);

// Route::get('/dashboard-getPromotions', [DashboardController::class, 'dataPromotion']);

Route::middleware('auth')->group(function () {

    Route::prefix('salary-outlet')->group(function () {
        Route::get('/', [SalarOutletController::class, 'index'])->name('salaryOutletView');
        Route::get('/outlets', [SalarOutletController::class, 'getDataSalaryOutlet']);
        Route::post('/store-salary-outlet', [SalarOutletController::class, 'store'])->name('store-salary-outlet.store');
        Route::get('/list', [SalarOutletController::class, 'getList']);
        Route::delete('/delete/{id}', [SalarOutletController::class, 'destroy']);
    });

    Route::prefix('target-outlet')->group(function () {
        Route::get('/', [TargetOutletController::class, 'index'])->name('targetOutletView');
        Route::get('/getDataOutlet', [TargetOutletController::class, 'getDataOutlet'])->name('getDataOutlet');
        Route::post('/target-outlet', [TargetOutletController::class, 'store'])->name('target-outlet.store');
        Route::get('/getDataTergetSales', [TargetOutletController::class, 'show'])->name('getDataTergetSales');
    });
    
    Route::prefix('menu-template')->group(function () {
        Route::get('/', [MenuTemplateControllers::class, 'index'])->name('menu-template');
        Route::post('/import', [MenuTemplateControllers::class, 'importMenuTemplate'])->name('importMenuTemplate');
        Route::get('/getDataMenu', [MenuTemplateControllers::class, 'getData'])->name('getDataMenu');
        Route::get('/{id}/edit', [MenuTemplateControllers::class, 'edit']);
        Route::put('/{id}', [MenuTemplateControllers::class, 'update']);
        Route::delete('/deleteMenuTemplate/{id}', [MenuTemplateControllers::class, 'destroy']);
    });


    Route::prefix('competitor')->group(function () {
        Route::get('/', [KompetitorController::class, 'index'])->name('kompetitor');
        Route::post('/store', [KompetitorController::class, 'store']);
        Route::get('/show-competitor', [KompetitorController::class, 'getDataKompetitor']);
    });

    Route::prefix('user')->group(function () {
        Route::get('/', [UserControllers::class, 'apiGetData'])->name('dataUsers');
        Route::get('/get-edit-outlets', [UserControllers::class, 'getOutletsEdit']);
        Route::get('/get-outlets', [UserControllers::class, 'getOutlets']);
        Route::post('/store', [UserControllers::class, 'store']);
        Route::delete('/deleteUser/{id}', [UserControllers::class, 'destroy']);
        Route::get('/showUser/{id}', [UserControllers::class, 'show']);
        Route::put('/updateUser/{id}', [UserControllers::class, 'update']);
    });
    Route::get('/users', [UserControllers::class, 'index'])->name('users');

    Route::prefix('aktualAPI')->group(function () {
        Route::post('/import-srdr', [AktualController::class, 'importSrdr'])->name('srdr.import');
        Route::get('/importAktual', [AktualController::class, 'importAktual'])->name('importSrdr');
        Route::get('/{id}/edit', [AktualController::class, 'edit']);
        Route::put('/{id}', [AktualController::class, 'update']);
        Route::get('/', [AktualController::class, 'apiIndex'])->name('dataAktual');
        // Route::post('/', [AktualController::class, 'apiStore']); //matikan sementara karena dialihkan ke srdr
        Route::post('/', [AktualController::class, 'srdrStore']);
        Route::delete('/deleteAktual/{id}', [AktualController::class, 'destroy']);
        Route::get('/getDataFormAktual', [AktualController::class, 'getDataFormAktual'])->name('aktual');
        
        // Route::get('/download-template-DM', [AktualController::class, 'downloadTempletDM'])->name('download.template.dm');
        Route::get('/download-template', [AktualController::class, 'downloadTemplet'])->name('download.template');
    });
    Route::get('/aktual', [AktualController::class, 'index'])->name('aktual');

    // fungsi branch dan sub branch 
    Route::prefix('branch')->group(function () {
        Route::get('/', [BranchController::class, 'index'])->name('branch');
        Route::get('/brand/list', [BranchController::class, 'getBrandList'])->name('brand.list');
        Route::get('/list', [BranchController::class, 'getBranchData'])->name('branch.list');
        Route::post('/store', [BranchController::class, 'store'])->name('branch.store');
        
        Route::get('/sub', [BranchController::class, 'sub_index'])->name('sub_branch');
        Route::get('/sub-brand/list', [BranchController::class, 'getSubBranchList'])->name('branch.list');
        Route::get('/sub_brand/list-data', [BranchController::class, 'getSubBranchData'])->name('branch.list');
        Route::post('/sub_branch/store', [BranchController::class, 'sub_store'])->name('branch.sub_store');
        
    });
    Route::delete('/outlet/{id}', [OutletController::class, 'destroy']);
    Route::put('/outlet/update/{id}', [OutletController::class, 'update']);
    Route::post('/outlet/store', [OutletController::class, 'store'])->name('outlet_store');
    Route::get('/outlet', [OutletController::class, 'index'])->name('outlet');

    Route::put('/brand/deactivated/{id}', [BrandController::class, 'deactivate']);
    Route::delete('/brand/{id}', [BrandController::class, 'destroy']);
    Route::post('/brand/update', [BrandController::class, 'update'])->name('brand.update');
    Route::post('/brand/store', [BrandController::class, 'store'])->name('brand_store');
    Route::get('/brand', [BrandController::class, 'index'])->name('brand');

    Route::prefix('promosiAPI')->group(function () {
        Route::get('/export-data-promosi', [EventController::class, 'apiExportgetData'])->name('exportPromosi');
        Route::get('/promosi/export', [EventController::class, 'export'])->name('promosi.export');
        Route::get('/event/data', [EventController::class, 'getDataEvent'])->name('event.data');
        Route::get('/menu-template', [EventController::class, 'getMenuTemplate']);
    });
    Route::get('/promosi/{id}', [EventController::class, 'edit']);
    Route::get('/get-outlet-edit/{id}', [EventController::class, 'getOutletEditByBrand']);
    Route::get('/get-outlet/{id}', [EventController::class, 'getOutletByBrand']);
    Route::get('/marketing', [EventController::class, 'index'])->name('marketing');
    Route::post('/unggah_promosi', [EventController::class, 'store'])->name('post_promosi');
    Route::post('/promosi/update/{id}', [EventController::class, 'update'])->name('promosi.update');
    Route::put('/promosi/deactivated/{id}', [EventController::class, 'deactivated']);
    Route::delete('/promosi/{id}', [EventController::class, 'destroy']);

    
    Route::resource('devices', DeviceController::class);
    Route::get('device/scan/{name}', [DeviceController::class, 'scan'])->name('devices.scan');
    Route::put('device/update/{id}', [DeviceController::class, 'update'])->name('devices.update');
    Route::get('device/disconnect/{name}', [DeviceController::class, 'disconnect'])->name('devices.disconnect');
    Route::post('device/{name}/update-status', [DeviceController::class, 'updateStatus']);
    Route::get('device/history/{id}', [DeviceController::class, 'history'])->name('devices.history');
    Route::get('device/chats/{id}', [DeviceController::class, 'chats'])->name('devices.chats');
    Route::get('device/{deviceId}/chats/{outboxNumber}', [DeviceController::class, 'showChat'])->name('devices.showChat');

    Route::resource('outbox', OutboxController::class);
});

require __DIR__.'/auth.php';
