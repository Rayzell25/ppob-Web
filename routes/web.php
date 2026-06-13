<?php

use Illuminate\Support\Facades\Route;

use App\Livewire\Storefront;
use App\Livewire\AdminDashboard;

use Illuminate\Support\Facades\Auth;

Route::get('/', Storefront::class)->name('home');
Route::get('/admin', AdminDashboard::class)->name('admin.dashboard');
Route::get('/login', [App\Http\Controllers\AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);
Route::get('/register', [App\Http\Controllers\AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [App\Http\Controllers\AuthController::class, 'register']);
Route::any('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout');

Route::get('/auth/{provider}/redirect', [App\Http\Controllers\SocialLoginController::class, 'redirect'])->name('social.redirect');
Route::get('/auth/{provider}/callback', [App\Http\Controllers\SocialLoginController::class, 'callback'])->name('social.callback');
