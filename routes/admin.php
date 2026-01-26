<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;


Auth::routes();
Auth::routes(['register' => false, 'verify' => false]);

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [\App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [\App\Http\Controllers\Auth\LoginController::class, 'login']);

Route::get('/register', [\App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [\App\Http\Controllers\Auth\RegisterController::class, 'register']);


// Social Login with google and facebook
Route::get('auth/redirect/{provider}', [App\Http\Controllers\Admin\SocialLoginController::class, 'redirectToProvider'])->name('redirectToProvider');
Route::get('auth/callback/{provider}', [App\Http\Controllers\Admin\SocialLoginController::class, 'handleProviderCallback']);

Route::group(['middleware' => ['auth'], 'as' => 'admin.', 'prefix' => 'admin'], function () {

    // Dashboard
    Route::get('dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    // Roles
    Route::resource('role', App\Http\Controllers\Admin\RoleController::class);

    //Pages
    Route::put('page/status/{id}', [App\Http\Controllers\Admin\PageController::class, 'status'])->name('page.status');
    Route::resource('page', App\Http\Controllers\Admin\PageController::class);

    //Users
    Route::resource('user', App\Http\Controllers\Admin\UserController::class);
    Route::put('user/status/{id}', [App\Http\Controllers\Admin\UserController::class, 'status'])->name('user.status');
    Route::get('user/remove-image/{id}', [App\Http\Controllers\Admin\UserController::class, 'removeImage'])->name('user.removeImage');

    //User_Profile
    Route::get('profile', [App\Http\Controllers\Admin\UserController::class, 'profile'])->name('user.profile');
    Route::get('profile/edit', [App\Http\Controllers\Admin\UserController::class, 'editProfile'])->name('user.profile-edit');
    Route::get('get-state', [App\Http\Controllers\Admin\UserController::class, 'getStates'])->name('user.get-states');
    Route::put('update-profile/{user}', [App\Http\Controllers\Admin\UserController::class, 'updateProfile'])->name('user.update-profile');
    Route::put('update-email/{user}', [App\Http\Controllers\Admin\UserController::class, 'updateEmail'])->name('user.update-email');
    Route::put('update-password/{user}', [App\Http\Controllers\Admin\UserController::class, 'updatePassword'])->name('user.update-password');

    // setting
    Route::get('settings', [App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
    Route::put('update/settings/{setting}', [App\Http\Controllers\Admin\SettingController::class, 'update'])->name('update.settings');

    Route::get('clear-cache', function () {
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        return redirect()->back()->with('success', 'Cache cleared successfully!');
    })->name('clear.cache');

    //MODUL_GENERATE_JANGAN_DIHAPUS
});

