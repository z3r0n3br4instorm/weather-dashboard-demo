<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SensorController;
use App\Http\Controllers\AlertThresholdController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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

// Public routes
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/sensor/{sensorId}/history', [DashboardController::class, 'getSensorHistoricalData'])->name('sensor.history');
Route::get('/weather', [DashboardController::class, 'getWeatherData'])->name('weather');

// Authentication routes
Route::get('/login', function() {
    return view('auth.login');
})->name('login');

Route::post('/login', function() {
    $credentials = request()->only('email', 'password');
    if (Auth::attempt($credentials)) {
        request()->session()->regenerate();
        return redirect()->intended('admin');
    }
    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ]);
})->name('login.post');

Route::post('/logout', function() {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout');

// Protected routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Admin Dashboard
    Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    // Sensor Management
    Route::prefix('admin/sensors')->name('admin.sensors.')->group(function () {
        Route::get('/', [SensorController::class, 'index'])->name('index');
        Route::get('/create', [SensorController::class, 'create'])->name('create');
        Route::post('/', [SensorController::class, 'store'])->name('store');
        Route::get('/{sensor}', [SensorController::class, 'show'])->name('show');
        Route::get('/{sensor}/edit', [SensorController::class, 'edit'])->name('edit');
        Route::put('/{sensor}', [SensorController::class, 'update'])->name('update');
        Route::delete('/{sensor}', [SensorController::class, 'destroy'])->name('destroy');
    });
    
    // Data Simulation
    Route::get('/admin/simulation', [AdminController::class, 'simulationPanel'])->name('admin.simulation');
    Route::post('/admin/simulation/toggle', [AdminController::class, 'toggleSimulation'])->name('admin.simulation.toggle');
    Route::post('/admin/simulation/generate', [AdminController::class, 'generateSimulatedData'])->name('admin.simulation.generate');
    
    // Alert Thresholds
    Route::get('/admin/alerts', [AdminController::class, 'alertThresholds'])->name('admin.alerts');
    Route::post('/admin/alerts', [AdminController::class, 'updateAlertThresholds'])->name('admin.alerts.update');
    
    // User Management (System Admin only)
    Route::middleware(['role:system_admin'])->group(function () {
        Route::get('/admin/users', [AdminController::class, 'userManagement'])->name('admin.users');
        Route::post('/admin/users', [AdminController::class, 'createUser'])->name('admin.users.create');
    });
    
    // Profile management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
