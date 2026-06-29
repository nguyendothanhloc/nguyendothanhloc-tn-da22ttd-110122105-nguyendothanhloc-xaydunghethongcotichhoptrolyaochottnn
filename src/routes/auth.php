<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    // Main registration page (shows role selection)
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');
    
    // Student registration (public)
    Route::get('register/student', [RegisteredUserController::class, 'createStudent'])
        ->name('register.student');
    Route::post('register/student', [RegisteredUserController::class, 'storeStudent']);
    
    // Teacher registration (public)
    Route::get('register/teacher', [RegisteredUserController::class, 'createTeacher'])
        ->name('register.teacher');
    Route::post('register/teacher', [RegisteredUserController::class, 'storeTeacher']);
    
    // Admin registration (public)
    Route::get('register/admin', [RegisteredUserController::class, 'createAdmin'])
        ->name('register.admin');
    Route::post('register/admin', [RegisteredUserController::class, 'storeAdmin']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
