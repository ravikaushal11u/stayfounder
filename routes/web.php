<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\Provider\DashboardController as ProviderDashboardController;
use App\Http\Controllers\Provider\LeadController as ProviderLeadController;
use App\Http\Controllers\Provider\PropertyController as ProviderPropertyController;
use App\Http\Controllers\Provider\SubscriptionController as ProviderSubscriptionController;
use App\Http\Controllers\Provider\VerificationController as ProviderVerificationController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SafetyReportController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Marketplace Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/stays', [PropertyController::class, 'index'])->name('properties.index');
Route::get('/stay/{slug}', [PropertyController::class, 'show'])->name('properties.show');

// Student Engagement Actions
Route::post('/stays/{property}/favorite', [FavoriteController::class, 'toggle'])->name('properties.favorite');
Route::post('/stays/{property}/inquire', [InquiryController::class, 'store'])->name('properties.inquire');
Route::post('/stays/{property}/report', [SafetyReportController::class, 'store'])->name('properties.report');

/*
|--------------------------------------------------------------------------
| Authentication Routes (Guests Only)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
    Route::get('/login/demo/{role}', [LoginController::class, 'demoLogin'])->name('login.demo');

    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register/student', [RegisterController::class, 'registerStudent'])->name('register.student');
    Route::post('/register/provider', [RegisterController::class, 'registerProvider'])->name('register.provider');
});

/*
|--------------------------------------------------------------------------
| Authenticated Shared Routes (Chat & Account)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'active'])->group(function () {
    Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

    // Chat / Direct Messaging Routes
    Route::get('/messages', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/messages/{conversation}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/messages/{conversation}', [ChatController::class, 'store'])->name('chat.store');
    Route::post('/stays/{property}/chat', [ChatController::class, 'start'])->name('chat.start');

    // Student Reviews & Ratings
    Route::post('/stays/{property}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::post('/reviews/{review}/reply', [ReviewController::class, 'reply'])->name('reviews.reply');
});

/*
|--------------------------------------------------------------------------
| Student Portal Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'active', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
    Route::get('/saved-stays', [FavoriteController::class, 'index'])->name('saved-stays');
    Route::get('/inquiries', [InquiryController::class, 'studentIndex'])->name('inquiries');
});

/*
|--------------------------------------------------------------------------
| Provider Portal Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'active', 'role:provider'])->prefix('provider')->name('provider.')->group(function () {
    Route::get('/dashboard', [ProviderDashboardController::class, 'index'])->name('dashboard');
    Route::resource('properties', ProviderPropertyController::class);
    Route::post('/properties/{property}/toggle-status', [ProviderPropertyController::class, 'toggleStatus'])->name('properties.toggle-status');
    Route::delete('/properties/{property}/images/{image}', [ProviderPropertyController::class, 'deleteImage'])->name('properties.images.delete');
    Route::post('/properties/{property}/images/{image}/primary', [ProviderPropertyController::class, 'setPrimaryImage'])->name('properties.images.primary');
    Route::get('/leads', [ProviderLeadController::class, 'index'])->name('leads.index');
    Route::patch('/leads/{inquiry}', [ProviderLeadController::class, 'update'])->name('leads.update');

    // Provider Verification & Trust Badge
    Route::get('/verification', [ProviderVerificationController::class, 'create'])->name('verification.create');
    Route::post('/verification', [ProviderVerificationController::class, 'store'])->name('verification.store');

    // Provider Subscriptions & Razorpay Monetization
    Route::get('/subscriptions', [ProviderSubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::post('/subscriptions/checkout/{plan}', [ProviderSubscriptionController::class, 'checkout'])->name('subscriptions.checkout');
    Route::post('/subscriptions/verify', [ProviderSubscriptionController::class, 'verify'])->name('subscriptions.verify');
});

// Razorpay Webhook Callback
Route::post('/api/webhooks/razorpay', [ProviderSubscriptionController::class, 'webhook'])->name('webhooks.razorpay');

/*
|--------------------------------------------------------------------------
| Admin Portal Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'active', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
});
