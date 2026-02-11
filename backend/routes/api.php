<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ComponentController;
use App\Http\Controllers\ComputerController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Authentication routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::get('/me', [AuthController::class, 'me']);

// Basic inventory API routes
Route::get('/assets', [InventoryController::class, 'getAssets']);
Route::get('/computers', [InventoryController::class, 'getComputers']);
Route::get('/departments', [InventoryController::class, 'getDepartments']);
Route::get('/users', [InventoryController::class, 'getUsers']);
Route::get('/stats', [InventoryController::class, 'getStats']);
Route::post('/assets', [InventoryController::class, 'storeAsset']);
Route::put('/assets/{id}', [InventoryController::class, 'updateAsset']);
Route::delete('/assets/{id}', [InventoryController::class, 'deleteAsset']);

// Component management routes
Route::get('/components', [ComponentController::class, 'index']);
Route::get('/components/stats', [ComponentController::class, 'stats']);
Route::post('/components/create', [ComponentController::class, 'create']);
Route::delete('/components/{id}/delete', [ComponentController::class, 'delete']);
Route::post('/components/processors', [ComponentController::class, 'addProcessor']);
Route::post('/components/motherboards', [ComponentController::class, 'addMotherboard']);
Route::post('/components/rams', [ComponentController::class, 'addRam']);
Route::post('/components/storages', [ComponentController::class, 'addStorage']);
Route::post('/components/video-cards', [ComponentController::class, 'addVideoCard']);
Route::post('/components/psus', [ComponentController::class, 'addPsu']);
Route::post('/components/dvd-roms', [ComponentController::class, 'addDvdRom']);
Route::put('/components/{type}/{id}', [ComponentController::class, 'updateComponent']);
Route::delete('/components/{type}/{id}', [ComponentController::class, 'deleteComponent']);

// Computer management routes
Route::get('/computers', [ComputerController::class, 'index']);
Route::get('/computers/list', [ComputerController::class, 'index']);
Route::post('/computers/create', [ComputerController::class, 'create']);
Route::delete('/computers/{id}/delete', [ComputerController::class, 'delete']);
Route::post('/computers/{id}/deploy', [ComputerController::class, 'deploy']);
Route::post('/computers/{id}/transfer', [ComputerController::class, 'transfer']);
Route::post('/computers/{id}/repair', [ComputerController::class, 'logRepair']);
Route::post('/computers/{id}/complete-repair', [ComputerController::class, 'completeRepair']);
Route::get('/computers/stats', [ComputerController::class, 'stats']);

// Reports and analytics routes
Route::get('/reports/pcs-per-department', [ReportController::class, 'pcsPerDepartment']);
Route::get('/reports/pcs-per-laboratory', [ReportController::class, 'pcsPerLaboratory']);
Route::get('/reports/component-usage', [ReportController::class, 'componentUsage']);
Route::get('/reports/repair-history', [ReportController::class, 'repairHistory']);
Route::get('/reports/deployment-history', [ReportController::class, 'deploymentHistory']);
Route::get('/reports/dashboard-stats', [ReportController::class, 'dashboardStats']);
Route::post('/reports/export', [ReportController::class, 'exportReport']);
