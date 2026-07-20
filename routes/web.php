<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminFundRequestController;
use App\Http\Controllers\Admin\AdminPaymentSettingController;
use App\Http\Controllers\Admin\AdminRankController;
use App\Http\Controllers\Admin\AdminRoiController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminWithdrawalController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RankController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\WalletController;
use Illuminate\Support\Facades\Route;

// Public marketing pages
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/about-us', [PageController::class, 'about'])->name('about');
Route::get('/service', [PageController::class, 'service'])->name('service');
Route::get('/plan', [PageController::class, 'plan'])->name('plan');
Route::get('/contact-us', [PageController::class, 'contact'])->name('contact');
Route::post('/contact-us', [PageController::class, 'submitContact'])->name('contact.submit');

// Guest-only auth routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.submit');

    Route::get('/signup', [RegisterController::class, 'show'])->name('signup');
    Route::get('/signup/verify-referral', [RegisterController::class, 'verifyReferralCode'])->name('signup.verify-referral');
    Route::post('/signup', [RegisterController::class, 'register'])->name('signup.submit');
    Route::get('/signup/verify', [RegisterController::class, 'showOtp'])->name('signup.otp');
    Route::post('/signup/verify', [RegisterController::class, 'verifyOtp'])->name('signup.otp.submit');
    Route::post('/signup/resend-otp', [RegisterController::class, 'resendOtp'])->name('signup.otp.resend');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Member dashboard
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('wallet')->name('wallet.')->group(function () {
        Route::redirect('/', '/wallet/add-fund')->name('index');
        Route::get('/add-fund', [WalletController::class, 'addFundPage'])->name('add-fund');
        Route::post('/add-fund', [WalletController::class, 'submitFundRequest'])->name('fund-request');
        Route::get('/withdraw', [WalletController::class, 'withdrawPage'])->name('withdraw.page');
        Route::post('/withdraw', [WalletController::class, 'requestWithdrawal'])->name('withdraw');
        Route::post('/withdraw/{withdrawal}/verify-otp', [WalletController::class, 'verifyWithdrawalOtp'])->name('withdraw.verify-otp');
        Route::get('/payment-history', [WalletController::class, 'paymentHistory'])->name('payment-history');
        Route::get('/withdrawal-requests', [WalletController::class, 'withdrawalRequests'])->name('withdrawal-requests');
    });

    Route::prefix('payment')->name('payment.')->group(function () {
        Route::get('/', [PaymentController::class, 'index'])->name('index');
        Route::get('/roi', [PaymentController::class, 'roi'])->name('roi');
        Route::get('/rank-reward', [PaymentController::class, 'rankReward'])->name('rank-reward');
        Route::get('/level', [PaymentController::class, 'level'])->name('level');
        Route::get('/direct', [PaymentController::class, 'direct'])->name('direct');
    });

    Route::get('/team', [TeamController::class, 'index'])->name('team.index');
    Route::get('/rank', [RankController::class, 'index'])->name('rank.index');
});

// Admin panel
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/create-dummy', [AdminUserController::class, 'createDummy'])->name('users.create-dummy');
    Route::post('/users/create-dummy', [AdminUserController::class, 'storeDummy'])->name('users.store-dummy');
    Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
    Route::post('/users/{user}/approve', [AdminUserController::class, 'approve'])->name('users.approve');
    Route::post('/users/{user}/toggle-roi', [AdminUserController::class, 'toggleRoi'])->name('users.toggle-roi');

    Route::get('/withdrawals', [AdminWithdrawalController::class, 'index'])->name('withdrawals.index');
    Route::post('/withdrawals/{withdrawal}/approve', [AdminWithdrawalController::class, 'approve'])->name('withdrawals.approve');
    Route::post('/withdrawals/{withdrawal}/reject', [AdminWithdrawalController::class, 'reject'])->name('withdrawals.reject');

    Route::get('/ranks', [AdminRankController::class, 'index'])->name('ranks.index');

    Route::get('/roi', [AdminRoiController::class, 'index'])->name('roi.index');
    Route::post('/roi/run', [AdminRoiController::class, 'run'])->name('roi.run');

    Route::get('/payment-settings', [AdminPaymentSettingController::class, 'edit'])->name('payment-settings.edit');
    Route::post('/payment-settings', [AdminPaymentSettingController::class, 'update'])->name('payment-settings.update');

    Route::get('/fund-requests', [AdminFundRequestController::class, 'index'])->name('fund-requests.index');
    Route::post('/fund-requests/{fundRequest}/approve', [AdminFundRequestController::class, 'approve'])->name('fund-requests.approve');
    Route::post('/fund-requests/{fundRequest}/reject', [AdminFundRequestController::class, 'reject'])->name('fund-requests.reject');
});
