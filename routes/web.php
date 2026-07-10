<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ComplaintRegisterController;

Route::redirect('/', '/admin');

// IT Complaint Register routes for dynamic dropdowns
Route::get('/employees/list', [EmployeeController::class, 'list']);

Route::get('/get-floors/{location}', [ComplaintRegisterController::class, 'getFloors']);
Route::get('/get-rooms/{location}/{floor}', [ComplaintRegisterController::class, 'getRooms']);
//IT Complaint Register store
Route::post('/complaintregister/store', [ComplaintRegisterController::class, 'store'])->name('complaintregister.store');
//Live screen
Route::get('/complaintregister/live-screen', [ComplaintRegisterController::class, 'liveScreen']);
Route::get('/complaintregister/live-data', [ComplaintRegisterController::class, 'liveData']);
Route::view('/complaintregister/live-iframe', 'complaintregister.live-iframe');
Route::post('/complaintregister/take-ticket/{ticket}', [ComplaintRegisterController::class, 'takeTicket']);
Route::post('/complaintregister/resolve-ticket/{ticket}', [ComplaintRegisterController::class, 'resolveTicket']);


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';