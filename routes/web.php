<?php

use Illuminate\Support\Facades\Route;

use App\Livewire\Storefront;
use App\Livewire\AdminDashboard;

use Illuminate\Support\Facades\Auth;

Route::get('/', Storefront::class)->name('home');
Route::get('/admin', AdminDashboard::class)->name('admin.dashboard');
Route::get('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout');

use App\Http\Controllers\Auth\SocialLoginController;

Route::get('/auth/{provider}/redirect', [SocialLoginController::class, 'redirect']);
Route::get('/auth/{provider}/callback', [SocialLoginController::class, 'callback']);
