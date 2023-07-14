<?php
//echo "unknown error occurred";exit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



Route::get('/', function() { return Response::json(['status'=>'active', 'access'=>'unauthenticated']);  })->name('status');


Route::group([
  'prefix' => '/register',
  'as' => 'register.',
], function () {
    Route::post('/get-form', [App\Http\Controllers\SignUpController::class, 'get_form']);
    Route::post('/submit', [App\Http\Controllers\SignUpController::class, 'submit_form']);
    Route::post('/resend', [App\Http\Controllers\SignUpController::class, 'resend_otps']);
    Route::post('/verify-otp', [App\Http\Controllers\SignUpController::class, 'verify_otp']);
	Route::get('/otp', [App\Http\Controllers\SignUpController::class, 'return_otp']);
    Route::get('/user-profile', [App\Http\Controllers\SignUpController::class, 'userProfile'])->name('userProfile');   
});

Route::group([
  'prefix' => '/login',
  'as' => 'login.',
], function () {
    Route::post('/get-form', [App\Http\Controllers\LoginRequestController::class, 'get_form']);
    Route::post('/submit', [App\Http\Controllers\LoginRequestController::class, 'submit_form']);
    Route::post('/resend', [App\Http\Controllers\LoginRequestController::class, 'resend_otps']);
    Route::post('/verify-otp', [App\Http\Controllers\LoginRequestController::class, 'verify_otp']);
	Route::get('/otp', [App\Http\Controllers\LoginRequestController::class, 'return_otp']);
    Route::get('/user-profile', [App\Http\Controllers\LoginRequestController::class, 'userProfile'])->name('userProfile');   
});

Route::group([
  'prefix' => '/auth',
  'as' => 'auth.',
], function () {
    Route::post('/login', [App\Http\Controllers\AuthController::class, 'login'])->name('login');
});

Route::group([
  'prefix' => '/wardrobe',
  'as' => 'wardrobe.',
], function () {
    Route::post('/add-item', [App\Http\Controllers\WardrobeController::class, 'add_item']);
    Route::post('/remove-item', [App\Http\Controllers\WardrobeController::class, 'remove_item']);
    Route::post('/remove-items', [App\Http\Controllers\WardrobeController::class, 'remove_items']);
    Route::post('/my', [App\Http\Controllers\WardrobeController::class, 'get_wardrobe']);
    Route::post('/my-detailed', [App\Http\Controllers\WardrobeController::class, 'get_details_wardrobe']);
});

Route::group([
  'prefix' => '/tags',
  'as' => 'tag.',
], function () {
    Route::get('/', [App\Http\Controllers\TagController::class, 'index'])->name('index');
    Route::get('/add', [App\Http\Controllers\TagController::class, 'add'])->name('add');
    Route::post('/search', [App\Http\Controllers\TagController::class, 'search_result']);
});

Route::group([
  'prefix' => '/brands',
  'as' => 'brand.',
], function () {
    Route::post('/search', [App\Http\Controllers\BrandController::class, 'search_result']);
});

Route::group([
  'prefix' => '/notify',
  'as' => 'notify.',
], function () {
    Route::post('/update', [App\Http\Controllers\NotificationController::class, 'update_id']);
    Route::post('/recent', [App\Http\Controllers\NotificationController::class, 'get_last']);
    Route::post('/delete', [App\Http\Controllers\NotificationController::class, 'delete_notif']);
    Route::post('/delete-all', [App\Http\Controllers\NotificationController::class, 'delete_all_notif']);
});

Route::group([
  'prefix' => '/preferences',
  'as' => 'preferences.',
], function () {
    Route::post('/search', [App\Http\Controllers\PreferenceController::class, 'get_list']);
});

Route::group([
  'prefix' => '/post-battles',
  'as' => 'postbattle.',
], function () {
    Route::get('/', [App\Http\Controllers\PostBattleController::class, 'index'])->name('index');
    Route::post('/list', [App\Http\Controllers\PostBattleController::class, 'paginated_list']);
    Route::post('/get', [App\Http\Controllers\PostBattleController::class, 'get_single']);
    Route::post('/store', [App\Http\Controllers\PostBattleController::class, 'store'])->name('store');
    Route::post('/like', [App\Http\Controllers\PostBattleController::class, 'like']);
    Route::post('/delete', [App\Http\Controllers\PostBattleController::class, 'delete_battle']);
});

Route::group([
  'prefix' => '/post-singles',
  'as' => 'postsingle.',
], function () {
    Route::get('/', [App\Http\Controllers\PostSingleController::class, 'index'])->name('index');
});

Route::group([
  'prefix' => '/profile',
  'as' => 'profile.',
], function () {
    Route::post('/get', [App\Http\Controllers\ProfileController::class, 'get_user']);
    Route::post('/update', [App\Http\Controllers\ProfileController::class, 'update_user']);
    Route::post('/create', [App\Http\Controllers\ProfileController::class, 'create_user']);
    Route::post('/posts', [App\Http\Controllers\PostSingleController::class, 'user_uploads']);
    Route::post('/likes', [App\Http\Controllers\PostSingleController::class, 'user_likes']);
    Route::post('/followers-following', [App\Http\Controllers\FollowController::class, 'followers_following']);
});

Route::group([
  'prefix' => '/user',
  'as' => 'user.',
], function () {
    Route::post('/follow', [App\Http\Controllers\FollowController::class, 'follow_user']);
});

Route::group([
  'prefix' => '/report',
  'as' => 'report.',
], function () {
    Route::post('/get-types', [App\Http\Controllers\ReportTypeController::class, 'get_reports']);
    Route::post('/submit', [App\Http\Controllers\PostReportController::class, 'submit_report']);
});

Route::group([
  'prefix' => '/whatshot',
  'as' => 'whatshot.',
], function () {
    Route::post('/list', [App\Http\Controllers\PostSingleController::class, 'whatshot_view']);
    Route::post('/related', [App\Http\Controllers\PostSingleController::class, 'whatshot_related']);
    Route::post('/details', [App\Http\Controllers\PostSingleController::class, 'show']);
});

Route::group([
  'prefix' => '/discovery',
  'as' => 'discovery.',
], function () {
    Route::post('/search', [App\Http\Controllers\DiscoveryController::class, 'search_result']);
});

Route::group([
  'prefix' => '/newsletter',
  'as' => 'newsletter.',
], function () {
    Route::post('/request', [App\Http\Controllers\NewsletterController::class, 'subscription_request'])->name('request');
});
Route::post('/status', function (Request $request) {
    return json_encode(['allowed'=>true]);
});


    Route::post('/testsms/{phone_code}/{phone}', [App\Http\Controllers\LoginRequestController::class, 'send_sms_test']);
    Route::post('/testemail/{email}', [App\Http\Controllers\LoginRequestController::class, 'send_email_test']);