<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\language\LanguageController;
use App\Http\Controllers\pages\HomePage;
use App\Http\Controllers\pages\Page2;
use App\Http\Controllers\pages\MiscError;
use App\Http\Controllers\authentications\LoginBasic;
use App\Http\Controllers\authentications\RegisterBasic;
use App\Http\Controllers\SurveyFormController;
use App\Http\Controllers\Admin\SurveyController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\StatisticsController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProfileController;

// Public Survey Routes (Root path)
Route::get('/', [SurveyFormController::class, 'index'])->name('survey.index');
Route::get('/survey/{survey}', [SurveyFormController::class, 'show'])->name('survey.show');
Route::post('/survey/{survey}/submit', [SurveyFormController::class, 'submit'])->name('survey.submit');
Route::get('/thank-you', [SurveyFormController::class, 'thankYou'])->name('survey.thank-you');

// Other Pages
Route::get('/page-2', [Page2::class, 'index'])->name('pages-page-2');

// locale
Route::get('/lang/{locale}', [LanguageController::class, 'swap']);
Route::get('/pages/misc-error', [MiscError::class, 'index'])->name('pages-misc-error');

// Authentication
Route::get('/auth/login-basic', [LoginBasic::class, 'index'])->name('auth-login-basic');
Route::post('/auth/login', [LoginBasic::class, 'login'])->name('auth.login');
Route::post('/auth/logout', [LoginBasic::class, 'logout'])->name('auth.logout');
Route::get('/auth/register-basic', [RegisterBasic::class, 'index'])->name('auth-register-basic');

// Admin Routes (Protected by authentication)
Route::middleware(['admin'])->prefix('admin')->group(function () {
    Route::get('dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');

    Route::resource('surveys', SurveyController::class)->names([
        'index' => 'admin.surveys.index',
        'create' => 'admin.surveys.create',
        'store' => 'admin.surveys.store',
        'show' => 'admin.surveys.show',
        'edit' => 'admin.surveys.edit',
        'update' => 'admin.surveys.update',
        'destroy' => 'admin.surveys.destroy',
    ]);

    Route::post('surveys/{survey}/toggle-status', [SurveyController::class, 'toggleStatus'])->name('admin.surveys.toggle-status');
    Route::post('surveys/{survey}/set-default', [SurveyController::class, 'setDefault'])->name('admin.surveys.set-default');

    Route::post('surveys/{survey}/questions', [QuestionController::class, 'store'])->name('admin.questions.store');
    Route::put('questions/{question}', [QuestionController::class, 'update'])->name('admin.questions.update');
    Route::delete('questions/{question}', [QuestionController::class, 'destroy'])->name('admin.questions.destroy');
    Route::post('surveys/{survey}/questions/reorder', [QuestionController::class, 'reorder'])->name('admin.questions.reorder');

    Route::get('surveys/{survey}/statistics', [StatisticsController::class, 'index'])->name('admin.statistics.index');
    Route::get('surveys/{survey}/statistics/export-excel', [StatisticsController::class, 'exportExcel'])->name('admin.statistics.export-excel');
    Route::get('surveys/{survey}/statistics/export-pdf', [StatisticsController::class, 'exportPdf'])->name('admin.statistics.export-pdf');

    // Users Management
    Route::resource('users', UserController::class)->names([
        'index' => 'admin.users.index',
        'create' => 'admin.users.create',
        'store' => 'admin.users.store',
        'show' => 'admin.users.show',
        'edit' => 'admin.users.edit',
        'update' => 'admin.users.update',
        'destroy' => 'admin.users.destroy',
    ]);
});

// Profile Routes (Protected by authentication)
Route::middleware(['admin'])->group(function () {
    Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('profile/change-password', [ProfileController::class, 'showChangePasswordForm'])->name('profile.change-password');
    Route::post('profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.change-password.update');
});
