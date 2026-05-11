<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\JadwalController;

// Auth
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Public Scanner (with schedule support)
Route::get('/scan', [AttendanceController::class, 'scan'])->name('scan');
Route::post('/scan/find', [AttendanceController::class, 'findStudent'])->name('scan.find');
Route::post('/scan/process', [AttendanceController::class, 'processScan'])->name('scan.process');
Route::post('/scan/verify-and-record', [AttendanceController::class, 'verifyAndRecord'])->name('scan.verify');

// Protected Routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Students
    Route::resource('students', StudentController::class);
    
    // Academic Masters
    Route::resource('dosens', DosenController::class);
    Route::resource('jadwals', JadwalController::class);
    
    // Attendance History
    Route::get('/attendance/history', [AttendanceController::class, 'history'])->name('attendance.history');
    Route::get('/attendance/print', [AttendanceController::class, 'print'])->name('attendance.print');
    Route::delete('/attendance/{id}', [AttendanceController::class, 'destroy'])->name('attendance.destroy');

});