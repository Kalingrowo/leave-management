<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\UserAccess\UserAccessController;
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

Auth::routes([
    'register' => false,
    'reset' => false,
    'confirm' => false
]);

Route::middleware('auth:sanctum')->group(function () {
    // authenticate user
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('logout', [LoginController::class, 'logout']);

    // roles and permissions
    Route::prefix('user-access')->name('user_access.')->group(function () {
        Route::get('get-all-permissions', [UserAccessController::class, 'getAllPermissions'])->name('get_all_permissions');
        Route::get('get-all-roles', [UserAccessController::class, 'getAllRoles'])->name('get_all_roles');
        Route::get('get-role-detail/{id}', [UserAccessController::class, 'getRoleDetail'])->name('get_role_detail');
    });
});
