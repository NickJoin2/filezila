<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WaiterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


//Route::prefix('api-cafe')->group(function() {
   Route::post('login', [UserController::class, 'login']);

   Route::middleware('auth:sanctum')->group(function() {
       Route::middleware('ability:admin')->get('user', [AdminController::class, 'read']);
       Route::middleware('ability:admin')->post('user', [AdminController::class, 'create']);
       Route::middleware('ability:admin')->post('work-shift', [AdminController::class, 'work_shift']);
       Route::middleware('ability:admin')->get('work-shift/{id}/open', [AdminController::class, 'work_shift_open']);
       Route::middleware('ability:admin')->get('work-shift/{id}/close', [AdminController::class, 'work_shift_close']);
       Route::middleware('ability:admin')->post('work-shift/{id}/user', [AdminController::class, 'work_shift_user']);
//       Route::middleware('ability:admin')->post('work-shift/{id}/order', [AdminController::class, 'work-shift_order']);

       Route::middleware('ability:waiter')->post('order', [WaiterController::class, 'order_create']);
       Route::middleware('ability:waiter')->get('order/{id}', [WaiterController::class, 'order_find']);
       Route::middleware('ability:waiter')->get('work-shift/{id}/order', [WaiterController::class, 'order_all']);
       Route::middleware('ability:waiter')->post('order/{id}/position', [WaiterController::class, 'order_position']);
       Route::middleware('ability:waiter')->delete('order/{order}/position/{position}', [WaiterController::class, 'order_position_delete']);


       Route::get('logout', [UserController::class, 'logout']);







       Route::middleware('ability:admin')->get('user/{id}', [AdminController::class, 'user_one']);
   });
//});
