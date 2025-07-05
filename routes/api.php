<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CessationController;
use App\Http\Controllers\CongeController;
use App\Http\Controllers\EmployesController;
use App\Http\Controllers\PermissionRoleController;
use App\Http\Controllers\UserController;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;



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

Route::get('/test-db', function () {
    try {
        DB::connection()->getPdo();
        return response()->json(['success' => true, 'message' => 'Connexion réussie à PostgreSQL 🎉']);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Échec de connexion',
            'error' => $e->getMessage(),
            'used_connection' => config('database.default')
        ]);
    }
});

Route::get('/force-reset-users', function () {
    try {
        DB::statement('DROP TABLE IF EXISTS users CASCADE;');
        return response()->json(['message' => 'Table users supprimée avec succès']);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});

Route::get('/force-reset-db', function () {
    try {
        DB::statement('DROP SCHEMA public CASCADE');
        DB::statement('CREATE SCHEMA public');
        Artisan::call('migrate', ['--force' => true]);

        return response()->json(['message' => '🎉 Base de données réinitialisée et migrations relancées']);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});

Route::get('/nuclear-reset', function () {
    try {
        // Liste toutes les tables existantes
        $tables = DB::select("SELECT tablename FROM pg_tables WHERE schemaname = 'public'");

        foreach ($tables as $table) {
            Schema::drop($table->tablename);
        }

        // Relancer les migrations sans transaction cassée
        Artisan::call('migrate', ['--force' => true]);

        return response()->json(['message' => '🚀 Toutes les tables supprimées et recréées avec succès']);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
    }
});

Route::get('/drop-roles', function () {
    try {
        Schema::dropIfExists('roles');
        return response()->json(['message' => '✅ Table roles supprimée avec succès']);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});


Route::get('/migrate', function () {
    try {
        Artisan::call('migrate', ['--force' => true]);
        return response()->json(['message' => 'Migrations exécutées avec succès']);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});


Route::get('/seed-user', function () {
    $user = User::firstOrCreate(
        ['email' => 'rh1@gmail.com'],
        [
            'name' => 'Koris',
            'password' => Hash::make('password123'),
            'role_id' => 1,
            'status' => 'active',
            'firstConnexion' => false
        ]
    );

    return response()->json($user);
});

Route::get('/seed-role', function () {
    $user = Role::firstOrCreate(
        [
            'name' => 'admin'
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
