<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HackathonController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RuleController;
use App\Http\Controllers\ThemeController;
use App\Http\Middleware\JwtMiddleware;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::get('/', function () {
    return response()->json('hello world');
});

Route::get('/protected-route', function () {
    return response()->json(['message' => 'You have access!']);
})->middleware('jwt');

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware([JwtMiddleware::class])->group(function () {
    Route::get('user', [AuthController::class, 'getUser']);
    Route::post('logout', [AuthController::class, 'logout']);
});

Route::put('users/{id}/roles', [RoleController::class, 'update'])->middleware('role:organisateur');

// ->middleware('role:organisateur');
// ->middleware('role:organisateur');
// ->middleware('role:organisateur');
// ->middleware('role:organisateur');

Route::get('themes', [ThemeController::class, 'index']);
Route::post('themes', [ThemeController::class, 'store']);
Route::put('themes/{id}', [ThemeController::class, 'update']);
Route::delete('themes/{id}', [ThemeController::class, 'delete']);


Route::get('hackathon', [HackathonController::class, 'index']);
Route::post('hackathon', [HackathonController::class, 'store']);
Route::put('hackathon/{id}', [HackathonController::class, 'update']);
Route::delete('hackathon/{id}', [HackathonController::class, 'delete']);


Route::get('rules', [RuleController::class, 'index']);
Route::post('rules', [RuleController::class, 'store']);
Route::put('rules/{id}', [RuleController::class, 'update']);
Route::delete('rules/{id}', [RuleController::class, 'delete']);
