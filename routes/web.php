<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [App\Http\Controllers\NewsletterController::class, 'subscriptionPage']);

Auth::routes();

Route::group([
  'prefix' => '/images',
  'as' => 'images.',
], function () {
	//Route::resource('images', App\Http\Controllers\ImageController::class);
	Route::get('/{id}', [App\Http\Controllers\ImageController::class, 'show'])->name('show');
});
Route::get('/home', [App\Http\Controllers\NewsletterController::class, 'subscriptionPage'])->name('home');
Route::get('/policy', [App\Http\Controllers\HomeController::class, 'policyPage'])->name('policy');
Route::get('/terms', [App\Http\Controllers\HomeController::class, 'termsPage'])->name('terms');

Route::group([
  'prefix' => '/auth',
  'as' => 'auth.',
], function () {
	
Route::get('/google/verify/{event}/{ticket}', [App\Http\Controllers\AuthController::class, 'redirectToGoogle'])->name('redirectToGoogle');
Route::get('/google/callback', [App\Http\Controllers\AuthController::class, 'handleGoogleCallback'])->name('redirectToGoogleCallback');

Route::get('/facebook/verify/{event}/{ticket}', [App\Http\Controllers\AuthController::class, 'redirectToFacebook'])->name('redirectToFacebook');
Route::get('/facebook/callback', [App\Http\Controllers\AuthController::class, 'handleFacebookCallback'])->name('redirectToFacebookCallback');

Route::get('/apple/verify/{event}/{ticket}', [App\Http\Controllers\AuthController::class, 'redirectToApple'])->name('redirectToApple')->middleware('\App\Http\Middleware\SameSiteNoneMiddleware::class');
Route::match(['get', 'post'], '/apple/callback', [App\Http\Controllers\AuthController::class, 'handleAppleCallback'])->name('redirectToAppleCallback')->middleware('\App\Http\Middleware\SameSiteNoneMiddleware::class');

});




//to be removed
Route::get('post-battles/{postBattle}', [App\Http\Controllers\PostBattleController::class, 'show']);
Route::get('notify/{notifiable}', [App\Http\Controllers\NotificationController::class, 'run_test']);