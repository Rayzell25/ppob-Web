<?php

use Illuminate\Support\Facades\Route;

use App\Livewire\Storefront;
use App\Livewire\AdminDashboard;

Route::get('/', Storefront::class)->name('home');
Route::get('/admin', AdminDashboard::class)->name('admin.dashboard');
