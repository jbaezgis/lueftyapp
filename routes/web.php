<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\User;
use App\Http\Controllers\TransferController;
use App\Http\Livewire\Bookings;
use App\Http\Livewire\Dashboard;
use App\Http\Livewire\ShowBooking;
use App\Http\Livewire\Home;
use App\Http\Livewire\BookingForm;
use App\Http\Livewire\BookingDetails;
use App\Http\Livewire\ContactForm;
use App\Http\Livewire\AboutUs;
use App\Http\Livewire\ContactUs;
use App\Http\Livewire\PrivacyPolicy;


// Lang
Route::get('locale/{locale}', function($locale){
    Session::put('locale', $locale);
    return redirect()->back();
});

Route::prefix('{language}')->group(function () {
});

// Route::middleware(['auth:sanctum', 'verified'])->get('/', [HomeController::class, 'home'])->name('home');
// Route::get('/', Home::class);
Route::get('/', Home::class);
Route::get('/about-us', AboutUs::class);
Route::get('/contact-us', ContactUs::class);
Route::get('/privacy-policy', PrivacyPolicy::class);

// Contact Form
Route::get('/contact', ContactForm::class);

// Booking process
Route::get('/booking-form/{id}', BookingForm::class);
Route::get('/booking-details/{id}', BookingDetails::class);
Route::patch('/booking/{id}', [TransferController::class, 'update'])->name('booking-update');


Route::post('booking/oneway', [TransferController::class, 'oneway'])->name('transer_oneway');
Route::post('booking/roundtrip', [TransferController::class, 'roundtrip'])->name('transer_roundtrip');

// Administratio
Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/dashboard', Dashboard::class);
    Route::get('/bookings', Bookings::class);
    Route::get('/booking/{id}', ShowBooking::class);
});
