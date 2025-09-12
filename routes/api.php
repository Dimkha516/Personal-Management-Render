<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CessationController;
use App\Http\Controllers\ChauffeurController;
use App\Http\Controllers\CongeController;
use App\Http\Controllers\DisponibiliteController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\EmployesController;
use App\Http\Controllers\FonctionController;
use App\Http\Controllers\JoursExclusController;
use App\Http\Controllers\OrdreMissionController;
use App\Http\Controllers\PermissionRoleController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\StatistiqueController;
use App\Http\Controllers\TypeAgentController;
use App\Http\Controllers\TypesCongesController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VehiculeController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;


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

//1---------------------------------------------
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


Route::get('/migrate', function () {
    try {
        Artisan::call('migrate', ['--force' => true]);
        return response()->json(['message' => 'Migrations exécutées avec succès']);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});
Route::get('/list-tables', function () {
    //---------------------------------------------


    //2---------------------------------------------
    $tables = DB::select("SELECT tablename FROM pg_tables WHERE schemaname = 'public'");
    return collect($tables)->pluck('tablename');
});

Route::get('/rebuild-config', function () {
    Artisan::call('config:clear');
    Artisan::call('config:cache');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    return ['message' => '✅ Cache Laravel régénéré'];
});

//-----------------------------------------------------SEEDERS6------------------------------------------

//-----------------------------------------------------SEEDERS-END------------------------------------------


Route::prefix('v1')->group(function () {
    //--------------- Permissions and Roles Routes ---------------
    Route::prefix('permissions')->group(function () {
        Route::get('/roles-permissions', [PermissionRoleController::class, 'index']);
    });

    //--------------- Authentication Routes ---------------
    Route::prefix('auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login']);
        Route::middleware('auth.expirable')->post('/logout', [AuthController::class, 'logout']);
        Route::middleware('auth.expirable')->get("/connectedUser", [AuthController::class, 'connectedUser']);
    });

    //--------------- Users Routes ---------------
    Route::prefix('users')->group(function () {
        Route::middleware('auth.expirable')->get("/all", [UserController::class, 'index']);
        Route::middleware('auth.expirable')->post('/create-employe-account', [UserController::class, 'createUserForEmploye']);
        // Route::post('/create-employe-account', [UserController::class, 'createUserForEmploye']);
        Route::middleware('auth.expirable')->get("/{id}", [UserController::class, 'show']);
        Route::middleware('auth.expirable')->put("/{id}", [UserController::class, 'update']);
        Route::middleware('auth.expirable')->delete("/{id}", [UserController::class, 'destroy']);
        Route::post('/change-password/{id}', [UserController::class, 'changePassword']);
        // Route::middleware(['auth.expirable'])->post('/change-password', [UserController::class, 'changePassword']);
        // Route::post('/change-password', [UserController::class, 'changePassword']);
    });

    //--------------- Employes Routes ---------------
    Route::prefix('employes')->group(function () {
        Route::middleware(['auth.expirable'])->get("/", [EmployesController::class, 'index']);
        Route::middleware(['auth.expirable'])->get("/soldeConge", [EmployesController::class, 'getSoldeConge']);
        Route::middleware('auth.expirable')->get("/{id}", [EmployesController::class, 'show']);
        Route::middleware('auth.expirable')->post("/", [EmployesController::class, 'store']);
        Route::middleware('auth.expirable')->put("/{id}", [EmployesController::class, 'update']);
        Route::get('/dossiers/{id}', [EmployesController::class, 'getEmployeDoc']);
    });

    //--------------- Conges Routes ---------------
    Route::prefix('conges')->group(function () {
        Route::middleware(['auth.expirable'])->get("/", [CongeController::class, 'index']);
        Route::middleware('auth.expirable')->get("/mesConges", [CongeController::class, 'mesConges']);
        Route::middleware('auth.expirable')->get("/{id}", [CongeController::class, 'show']);

        Route::middleware('auth.expirable')->post("/", [CongeController::class, 'store']);
        Route::middleware('auth.expirable')->post("/traiterDemandeConge/{id}", [CongeController::class, 'traiter']);
        Route::middleware('auth.expirable')->post("/demandeForEmploye/{id}", [CongeController::class, 'demandeForEmploye']);


        Route::middleware('auth.expirable')->put("/{id}", [CongeController::class, 'update']);
        Route::middleware('auth.expirable')->delete("/{id}", [congeController::class, 'destroy']);
    });

    //--------------- Cessations Routes ---------------
    Route::prefix('cessations')->group(function () {
        Route::middleware('auth.expirable')->get("/", [CessationController::class, 'index']);
        Route::middleware('auth.expirable')->get("/mesCessations", [CessationController::class, 'mesCessations']);
        Route::middleware('auth.expirable')->get("/{id}", [CessationController::class, 'show']);
        Route::middleware('auth.expirable')->post("/demandeForEmploye/{id}", [CessationController::class, 'demandeForEmploye']);


        Route::middleware('auth.expirable')->post("/", [CessationController::class, 'store']);
        Route::middleware('auth.expirable')->post("/traiterDemandeCessation/{id}", [CessationController::class, 'traiter']);


        Route::middleware('auth.expirable')->put("/{id}", [CessationController::class, 'update']);
        Route::middleware('auth.expirable')->delete("/{id}", [CessationController::class, 'destroy']);
    });


    //--------------- DISPONIBILITES Routes ---------------
    Route::prefix('disponibilites')->group(function () {
        Route::middleware('auth.expirable')->get("/", [DisponibiliteController::class, 'index']);
        Route::middleware('auth.expirable')->get("/mesDisponibilites", [DisponibiliteController::class, 'mesDisponibilites']);
        Route::middleware('auth.expirable')->get("/{id}", [DisponibiliteController::class, 'show']);

        Route::middleware('auth.expirable')->post("/", [DisponibiliteController::class, 'store']);
        Route::middleware('auth.expirable')->post("/traiterDemandeCessation/{id}", [DisponibiliteController::class, 'traiter']);
    });

    //--------------- SERVICES Routes ---------------
    Route::prefix('services')->group(function () {
        // Route::middleware('auth.expirable')->get("/", [ServiceController::class, 'index']);
        Route::get("/", [ServiceController::class, 'index']);
        Route::get("/addChefService/{id}", [ServiceController::class, 'addChefService']);
        Route::middleware('auth.expirable')->get("/{id}", [ServiceController::class, 'show']);
        // Route::middleware('auth.expirable')->get("/addChefService/{id}", [ServiceController::class, 'addChefService']);
        Route::middleware('auth.expirable')->post("/", [ServiceController::class, 'store']);
    });
    //--------------- FONCTIONS Routes ---------------
    Route::prefix('fonctions')->group(function () {
        // Route::middleware('auth.expirable')->get("/", [ServiceController::class, 'index']);
        Route::get("/", [FonctionController::class, 'index']);
        Route::middleware('auth.expirable')->get("/{id}", [FonctionController::class, 'show']);
        Route::middleware('auth.expirable')->post("/", [FonctionController::class, 'store']);
    });

    //--------------- TYPES AGENT Routes ---------------
    Route::prefix('typesAgent')->group(function () {
        Route::get("/", [TypeAgentController::class, 'index']);
        Route::get("/{id}", [TypeAgentController::class, 'show']);
        Route::post("/", [TypeAgentController::class, 'store']);
    });


    //--------------- TYPES CONGES Routes ---------------
    Route::prefix('typesConges')->group(function () {
        Route::get("/", [TypesCongesController::class, 'index']);
        Route::get("/{id}", [TypesCongesController::class, 'show']);
        Route::post("/", [TypesCongesController::class, 'store']);
    });

    //--------------- STATISTICS Routes ---------------
    Route::prefix('stats')->group(function () {
        Route::get('/all', [StatistiqueController::class, 'index']); // Toutes les stats
        Route::middleware('auth.expirable')->get("/myStats", [StatistiqueController::class, 'connectedUserStats']);
        Route::get('/{entity}', [StatistiqueController::class, 'show']); // Stats ciblées
    });


    //--------------- DOCUMENTS Routes ---------------
    Route::prefix('documents')->group(function () {
        Route::get('/', [DocumentController::class, 'index']);
        Route::get('/typeDocument', [DocumentController::class, 'allTypeDocument']);
        Route::post('/', [DocumentController::class, 'store']);
        Route::post('/typeDocument', [DocumentController::class, 'createDocumentType']);
    });

    //--------------- VEHICULES Routes ---------------
    Route::prefix('vehicules')->group(function () {
        Route::get("/", [VehiculeController::class, 'index']);
        Route::get("/{id}", [VehiculeController::class, 'show']);
        Route::post("/", [VehiculeController::class, 'store']);
    });

    //--------------- Chauffeur Routes ---------------
    Route::prefix('chauffeurs')->group(function () {
        Route::get("/", [ChauffeurController::class, 'index']);
        Route::get("/{id}", [ChauffeurController::class, 'show']);
        Route::post("/", [ChauffeurController::class, 'store']);
    });

    //--------------- ORDRE MISSION Routes ---------------
    Route::prefix('ordresMission')->group(function () {
        // Route::middleware('auth.expirable')->get("/", [OrdreMissionController::class, 'index']);
        Route::get("/", [OrdreMissionController::class, 'index']);
        Route::get("/{id}", [OrdreMissionController::class, 'show']);
        Route::middleware('auth.expirable')->post("/", [OrdreMissionController::class, 'store']);
    });

    //--------------- JOURS EXCLUS Routes ---------------
    Route::prefix('joursExclus')->group(function () {
        Route::get("/", [JoursExclusController::class, 'index']);
        Route::get("/{id}", [JoursExclusController::class, 'show']);
        Route::post("/", [JoursExclusController::class, 'store']);
        Route::put('/{id}', [JoursExclusController::class, 'update']);
        Route::delete("/{id}", [JoursExclusController::class, 'destroy']);
    });
});


/*
Créer la table jours_exclus:
Méthodes: 
	-- Ajouter jour exclu: Date, Motif
	-- GetAll Jours exclus
	-- Retirer jour exclus
    -- Ajouter plusieurs jours exclus en même temps pour le calendrier

A demander: A qui envoyer la notification de création demande OM si chef de service null ?  Pour le moment si chef
de service null, je l'envoi au DG directement. 
----------------------------------------------------------------------------
Congé annuel éviter les nombres de jour trop élevés.
Mot de passe oublié.
Changement statut matrimoniale
En cas de CDI pas de durée.
Modifier Infos Employé.
*/
