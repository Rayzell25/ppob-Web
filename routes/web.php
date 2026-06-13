<?php

use Illuminate\Support\Facades\Route;

use App\Livewire\Storefront;
use App\Livewire\AdminDashboard;

Route::get('/', Storefront::class)->name('home');
Route::get('/admin', AdminDashboard::class)->name('admin.dashboard');

use App\Http\Controllers\Auth\SocialLoginController;

Route::get('/auth/{provider}/redirect', [SocialLoginController::class, 'redirect']);
Route::get('/auth/{provider}/callback', [SocialLoginController::class, 'callback']);
