<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ConnectionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\AppoinmentController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    $role = Auth::User();
    if ($role == null) {
        return redirect('/login');
    }
    if ($role->role == 'admin') {
        return redirect(route('admin.dashboard'));
    }
    if ($role->role == 'user') {
        return redirect(route('user.dashboard'));
    }
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/user/dashboard', [UserController::class, 'index'])->name('user.dashboard');
    Route::resource('appointments', AppoinmentController::class);
    // fetch doctor availability days
    Route::get('doctor-availability/{doctorId}', [AppoinmentController::class, 'getDoctorAvailability']);
    // fetch available time slots for the selected date
    Route::get('available-slots/{doctorId}/{date}', [AppoinmentController::class, 'getAvailableSlots']);
    Route::get('/booked-appointments', [AppoinmentController::class, 'showBookedAppointments'])->name('appointments.booked');


    Route::get('/user/connections', [ConnectionController::class, 'index'])->name('user.connection');
    Route::post('/user/connect', [ConnectionController::class, 'create'])->name('user.connect');
    Route::post('/user/disconnect', [ConnectionController::class, 'destroy'])->name('user.disconnect');
    Route::post('/user/accept-connection', [ConnectionController::class, 'acceptRequest'])->name('user.accept-connection');
});

// Admin Routes
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::resource('doctors', DoctorController::class);
});

require __DIR__ . '/auth.php';
