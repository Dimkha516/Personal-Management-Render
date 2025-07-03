<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CessationController;
use App\Http\Controllers\CongeController;
use App\Http\Controllers\EmployesController;
use App\Http\Controllers\PermissionRoleController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

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


Route::get('/seed-user', function () {
    $user = User::firstOrCreate(
        ['email' => 'rh1@gmail.com'],
        [
            'password' => Hash::make('password123'),
            'status' => 'active',
            'firstConnexion' => false
        ]
    );

    return response()->json($user);
});

Route::prefix('v1')->group(function () {
    //--------------- Permissions and Roles Routes ---------------
    Route::prefix('permissions')->group(function () {
        Route::get('/roles-permissions', [PermissionRoleController::class, 'index']);
    });

    //--------------- Authentication Routes ---------------
    Route::prefix('auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login']);
        Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
        Route::middleware('auth:sanctum')->get("/connectedUser", [AuthController::class, 'connectedUser']);
    });

    //--------------- Users Routes ---------------
    Route::prefix('users')->group(function () {
        Route::middleware('auth:sanctum')->get("/all", [UserController::class, 'index']);
        Route::middleware('auth:sanctum')->post('/create-employe-account', [UserController::class, 'createUserForEmploye']);
        Route::middleware('auth:sanctum')->get("/{id}", [UserController::class, 'show']);
        Route::middleware('auth:sanctum')->put("/{id}", [UserController::class, 'update']);
        Route::middleware('auth:sanctum')->delete("/{id}", [UserController::class, 'destroy']);
        Route::middleware(['auth:sanctum'])->post('/change-password', [UserController::class, 'changePassword']);
        Route::post('/change-password', [UserController::class, 'changePassword']);
    });

    //--------------- Employes Routes ---------------
    Route::prefix('employes')->group(function () {
        Route::middleware('auth:sanctum')->get("/", [EmployesController::class, 'index']);
        Route::middleware('auth:sanctum')->get("/{id}", [EmployesController::class, 'show']);
        Route::middleware('auth:sanctum')->post("/", [EmployesController::class, 'store']);
        Route::middleware('auth:sanctum')->put("/{id}", [EmployesController::class, 'update']);
    });

    //--------------- Conges Routes ---------------
    Route::prefix('conges')->group(function () {
        Route::middleware('auth:sanctum')->get("/", [CongeController::class, 'index']);
        Route::middleware('auth:sanctum')->get("/mesConges", [CongeController::class, 'mesConges']);
        Route::middleware('auth:sanctum')->get("/{id}", [CongeController::class, 'show']);

        Route::middleware('auth:sanctum')->post("/", [CongeController::class, 'store']);
        Route::middleware('auth:sanctum')->post("/valider/{id}", [congeController::class, 'valider']);
        Route::middleware('auth:sanctum')->post("/reject/{id}", [congeController::class, 'rejeter']);

        Route::middleware('auth:sanctum')->put("/{id}", [CongeController::class, 'update']);
        Route::middleware('auth:sanctum')->delete("/{id}", [congeController::class, 'destroy']);
    });

    //--------------- Cessations Routes ---------------
    Route::prefix('cessations')->group(function () {
        Route::middleware('auth:sanctum')->get("/", [CessationController::class, 'index']);
        Route::middleware('auth:sanctum')->get("/mesCessations", [CessationController::class, 'mesCessations']);
        Route::middleware('auth:sanctum')->get("/{id}", [CessationController::class, 'show']);

        Route::middleware('auth:sanctum')->post("/", [CessationController::class, 'store']);
        Route::middleware('auth:sanctum')->post("/valider/{id}", [CessationController::class, 'valider']);
        Route::middleware('auth:sanctum')->post("/reject/{id}", [CessationController::class, 'rejeter']);

        Route::middleware('auth:sanctum')->put("/{id}", [CessationController::class, 'update']);
        Route::middleware('auth:sanctum')->delete("/{id}", [CessationController::class, 'destroy']);
    });
});
