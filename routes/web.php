<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LandingPageController;

Route::get('/', [LandingPageController::class, 'index'])->name('home');

// Dummy routes untuk mengatasi link mati
Route::get('/login', function () { return view('auth.login'); })->name('login');
Route::get('/register', function () { return view('auth.register'); })->name('register');
Route::get('/verify-email', function () { return view('auth.verify-email'); })->name('verify.email');
Route::get('/dashboard', function () { return "Halaman Dashboard - Tahap Pengembangan"; })->name('dashboard');
