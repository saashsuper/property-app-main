<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\BlockBuildingController;
use App\Http\Controllers\BlockUnitController;
use App\Http\Controllers\PWAController;


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

Auth::routes(['verify' => true]);

Route::get('index/{locale}',[App\Http\Controllers\HomeController::class, 'lang']);
Route::get('/', [App\Http\Controllers\HomeController::class, 'root'])->name('root');

// PWA Routes - must be before catch-all route
Route::get('/manifest.json', [PWAController::class, 'manifest'])->name('pwa.manifest');
Route::get('/sw.js', [PWAController::class, 'serviceWorker'])->name('pwa.service-worker');
Route::get('/offline', [PWAController::class, 'offline'])->name('pwa.offline');
Route::get('/api/pwa/install-status', [PWAController::class, 'installStatus'])->name('pwa.install-status');

// Dashboard Routes
Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::get('api/dashboard/stats', [App\Http\Controllers\DashboardController::class, 'getDashboardStats'])->name('api.dashboard.stats');
    Route::get('api/dashboard/issues-stats', [App\Http\Controllers\DashboardController::class, 'getIssuesStats'])->name('api.dashboard.issues-stats');
    Route::get('api/dashboard/recent-issues', [App\Http\Controllers\DashboardController::class, 'getRecentIssues'])->name('api.dashboard.recent-issues');
    Route::get('api/dashboard/recent-blocks', [App\Http\Controllers\DashboardController::class, 'getRecentBlocks'])->name('api.dashboard.recent-blocks');
});

// Block Management Routes
Route::middleware(['auth'])->group(function () {
    // Block Types - Admin only
    Route::resource('block-types', App\Http\Controllers\BlockTypeController::class)->middleware('role:Admin');
    Route::get('api/block-types', [App\Http\Controllers\BlockTypeController::class, 'getBlockTypes'])->name('api.block-types');
    
    // Blocks - Admin only for create, edit, delete
    Route::get('blocks', [App\Http\Controllers\BlockController::class, 'index'])->name('blocks.index');
    
    // Block management routes - Admin and Property Management roles
    Route::middleware(['role:Admin|Super Admin|Property manager|Office Administrator|Assistant Property Manager'])->group(function () {
        Route::get('blocks/create', [App\Http\Controllers\BlockController::class, 'create'])->name('blocks.create');
        Route::post('blocks', [App\Http\Controllers\BlockController::class, 'store'])->name('blocks.store');
        Route::get('blocks/{block}/edit', [App\Http\Controllers\BlockController::class, 'edit'])->name('blocks.edit');
        Route::put('blocks/{block}', [App\Http\Controllers\BlockController::class, 'update'])->name('blocks.update');
        Route::delete('blocks/{block}', [App\Http\Controllers\BlockController::class, 'destroy'])->name('blocks.destroy');
    });
    
    Route::get('blocks/{block}', [App\Http\Controllers\BlockController::class, 'show'])->name('blocks.show');
    Route::get('api/blocks', [App\Http\Controllers\BlockController::class, 'getBlocks'])->name('api.blocks');
    Route::get('api/blocks/{block}', [App\Http\Controllers\BlockController::class, 'getBlock'])->name('api.blocks.show');
    Route::get('api/blocks/{block}/units-autocomplete', [App\Http\Controllers\BlockController::class, 'getUnitsAutocomplete'])->name('api.blocks.units-autocomplete');
    Route::get('api/blocks/{block}/buildings', [App\Http\Controllers\BlockController::class, 'getBlockBuildings'])->name('api.blocks.buildings');
    
    // Block Images Routes
    Route::get('blocks/{block}/images', [App\Http\Controllers\BlockController::class, 'showImages'])->name('blocks.images');
    Route::post('blocks/{block}/images', [App\Http\Controllers\BlockController::class, 'uploadImages'])->name('blocks.images.upload');
    Route::delete('blocks/{block}/images', [App\Http\Controllers\BlockController::class, 'deleteImage'])->name('blocks.images.delete');
    Route::post('blocks/{block}/images/primary', [App\Http\Controllers\BlockController::class, 'setPrimaryImage'])->name('blocks.images.primary');
    
    // Work Orders
    Route::resource('work-orders', App\Http\Controllers\WorkOrderController::class)->except(['create', 'store', 'edit', 'update', 'destroy']);
    Route::post('work-orders/{workOrder}/reassign', [App\Http\Controllers\WorkOrderController::class, 'reassign'])->name('work-orders.reassign');
    
    // Work Order management routes (Admin only, not Contractor Admin)
    Route::middleware(['role:Admin'])->group(function () {
        Route::get('work-orders/create', [App\Http\Controllers\WorkOrderController::class, 'create'])->name('work-orders.create');
        Route::post('work-orders', [App\Http\Controllers\WorkOrderController::class, 'store'])->name('work-orders.store');
        Route::get('work-orders/{workOrder}/edit', [App\Http\Controllers\WorkOrderController::class, 'edit'])->name('work-orders.edit');
        Route::put('work-orders/{workOrder}', [App\Http\Controllers\WorkOrderController::class, 'update'])->name('work-orders.update');
        Route::delete('work-orders/{workOrder}', [App\Http\Controllers\WorkOrderController::class, 'destroy'])->name('work-orders.destroy');
    });
    
    Route::get('api/work-orders', [App\Http\Controllers\WorkOrderController::class, 'getWorkOrders'])->name('api.work-orders');
    Route::get('api/work-orders/{workOrder}', [App\Http\Controllers\WorkOrderController::class, 'getWorkOrder'])->name('api.work-orders.show');
    
    // Block Work Orders
    Route::resource('block-work-orders', App\Http\Controllers\BlockWorkOrderController::class);
    Route::get('api/block-work-orders', [App\Http\Controllers\BlockWorkOrderController::class, 'getBlockWorkOrders'])->name('api.block-work-orders');
    Route::get('api/block-work-orders/{blockWorkOrder}', [App\Http\Controllers\BlockWorkOrderController::class, 'getBlockWorkOrder'])->name('api.block-work-orders.show');
    
    // Site Visits (Block Visits)
    Route::resource('block-visits', App\Http\Controllers\BlockVisitController::class);

    // Block Inspections
    Route::resource('block-inspections', App\Http\Controllers\BlockInspectionController::class);
    Route::post('block-inspections/{blockInspection}/start', [App\Http\Controllers\BlockInspectionController::class, 'start'])->name('block-inspections.start');
    Route::post('block-inspections/{blockInspection}/complete', [App\Http\Controllers\BlockInspectionController::class, 'complete'])->name('block-inspections.complete');
    Route::post('block-inspections/store-from-modal', [App\Http\Controllers\BlockInspectionController::class, 'storeFromModal'])->name('block-inspections.store-from-modal');
    Route::get('api/blocks/{block}/inspections', [App\Http\Controllers\BlockInspectionController::class, 'getBlockInspections'])->name('api.blocks.inspections');

    // Block Issues
    Route::resource('block-issues', App\Http\Controllers\BlockIssueController::class);
    Route::delete('block-issues/images/{image}', [App\Http\Controllers\BlockIssueController::class, 'deleteImage'])->name('block-issues.delete-image');
    Route::post('block-issues/{blockIssue}/actions', [App\Http\Controllers\BlockIssueController::class, 'storeAction'])->name('block-issues.store-action');
    Route::post('block-issues/{blockIssue}/photos', [App\Http\Controllers\BlockIssueController::class, 'uploadPhotos'])->name('block-issues.upload-photos');
    Route::get('api/block-issues', [App\Http\Controllers\BlockIssueController::class, 'getBlockIssues'])->name('api.block-issues');
    Route::get('api/block-issues/{blockIssue}', [App\Http\Controllers\BlockIssueController::class, 'getBlockIssue'])->name('api.block-issues.show');
    Route::get('api/block-issues/{blockIssue}/photos', [App\Http\Controllers\BlockIssueController::class, 'getPhotos'])->name('api.block-issues.photos');
    Route::delete('api/block-issue-photos/{photo}', [App\Http\Controllers\BlockIssueController::class, 'deletePhoto'])->name('api.block-issue-photos.delete');
    Route::get('api/contact-methods-autocomplete', [App\Http\Controllers\BlockIssueController::class, 'getContactMethodsAutocomplete'])->name('api.contact-methods-autocomplete');
    Route::get('api/property-managers-autocomplete', [App\Http\Controllers\BlockIssueController::class, 'getPropertyManagersAutocomplete'])->name('api.property-managers-autocomplete');
    Route::get('api/block-unit-contact-details', [App\Http\Controllers\BlockIssueController::class, 'getBlockUnitContactDetails'])->name('api.block-unit-contact-details');
    
    // General Issues
    Route::resource('issues', App\Http\Controllers\IssueController::class);
    Route::get('api/issues', [App\Http\Controllers\IssueController::class, 'getIssues'])->name('api.issues');
    Route::get('api/issues/{issue}', [App\Http\Controllers\IssueController::class, 'getIssue'])->name('api.issues.show');
    
    // User Management
    Route::resource('users', App\Http\Controllers\UserController::class);
    Route::get('api/users', [App\Http\Controllers\UserController::class, 'getUsers'])->name('api.users');
    Route::get('api/users/{user}', [App\Http\Controllers\UserController::class, 'getUser'])->name('api.users.show');
    
    // User Types
    Route::resource('user-types', App\Http\Controllers\UserTypeController::class);
    Route::get('api/user-types', [App\Http\Controllers\UserTypeController::class, 'getUserTypes'])->name('api.user-types');
    Route::get('api/user-types/{userType}', [App\Http\Controllers\UserTypeController::class, 'getUserType'])->name('api.user-types.show');
    
    // Export Routes
    Route::prefix('export')->name('export.')->group(function () {
        Route::get('pdf/{type}', [App\Http\Controllers\ExportController::class, 'exportPdf'])->name('pdf');
        Route::get('excel/{type}', [App\Http\Controllers\ExportController::class, 'exportExcel'])->name('excel');
        Route::get('print/{type}', [App\Http\Controllers\ExportController::class, 'exportPrint'])->name('print');
    });

    Route::post('block-contractors', [\App\Http\Controllers\BlockContractorController::class, 'store'])->name('block-contractors.store');
    Route::put('block-contractors/{id}', [\App\Http\Controllers\BlockContractorController::class, 'update'])->name('block-contractors.update');
    Route::delete('block-contractors/{id}', [\App\Http\Controllers\BlockContractorController::class, 'destroy'])->name('block-contractors.destroy');
    Route::get('block-contractors/{id}', [\App\Http\Controllers\BlockContractorController::class, 'show'])->name('block-contractors.show');
});

// Block Information Routes
Route::resource('block-information', App\Http\Controllers\BlockInformationController::class);

// API Routes for DataTable refresh (no auth required for AJAX calls)
Route::get('block-information/block/{blockId}', [App\Http\Controllers\BlockInformationController::class, 'getBlockInformation'])->name('block-information.by-block');
Route::get('block-units/block/{blockId}', [App\Http\Controllers\BlockUnitController::class, 'getBlockUnits'])->name('block-units.by-block');
Route::get('block-contractors/block/{blockId}', [App\Http\Controllers\BlockContractorController::class, 'getBlockContractors'])->name('block-contractors.by-block');
Route::get('block-buildings/block/{blockId}', [App\Http\Controllers\BlockBuildingController::class, 'getBlockBuildings'])->name('block-buildings.by-block');
Route::get('block-visits/block/{blockId}', [App\Http\Controllers\BlockVisitController::class, 'getBlockVisits'])->name('block-visits.by-block');

// API Routes for address fields
Route::get('api/states/{countryId}', [App\Http\Controllers\BlockController::class, 'getStatesByCountry']);

// Block Building Management Routes
Route::middleware(['auth'])->group(function () {
    Route::resource('block-buildings', BlockBuildingController::class)->middleware('role:Admin');
});

// Block Unit Management Routes
Route::middleware(['auth'])->group(function () {
    Route::resource('block-units', BlockUnitController::class)->middleware('role:Admin');
    Route::post('block-units/upload', [BlockUnitController::class, 'upload'])->name('block-units.upload')->middleware('role:Admin');
    Route::get('block-units/template', [BlockUnitController::class, 'downloadTemplate'])->name('block-units.template')->middleware('role:Admin');
    Route::get('block-units/create-sample', [BlockUnitController::class, 'createSampleExcel'])->name('block-units.create-sample')->middleware('role:Admin');
    Route::get('block-units/test-phpspreadsheet', [BlockUnitController::class, 'testPhpSpreadsheet'])->name('block-units.test-phpspreadsheet')->middleware('role:Admin');
});

// Catch-all route for SPA - must be last
Route::get('{any}', [App\Http\Controllers\HomeController::class, 'index'])->where('any', '.*')->name('index');
Route::get('/blocks/{block}/information-table', [App\Http\Controllers\BlockController::class, 'blockInformationTable'])->name('blocks.information-table');