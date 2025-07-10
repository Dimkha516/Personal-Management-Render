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


Route::get('/migrate', function () {
    try {
        Artisan::call('migrate', ['--force' => true]);
        return response()->json(['message' => 'Migrations exécutées avec succès']);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});



Route::get('/list-tables', function () {
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

Route::get('/seed-roles', function () {
    try {
        $roles = ['admin', 'employe', 'rh', 'directeur'];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(['name' => $role]);
        }

        return response()->json(['success' => true, 'message' => '✅ Roles insérés avec succès']);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});

Route::get('/seed-permissions', function () {
    try {
        $permissions = ['voir_conges', 'valider_conges', 'gerer_utilisateurs', 'voir_statistiques'];

        foreach ($permissions as $perm) {
            DB::table('permissions')->updateOrInsert(['name' => $perm]);
        }

        return response()->json(['success' => true, 'message' => '✅ Permissions insérées avec succès']);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});

Route::get('/seed-role-permission', function () {
    try {
        $role = DB::table('roles')->where('name', 'admin')->first();
        $permissions = DB::table('permissions')->pluck('id');

        foreach ($permissions as $permId) {
            DB::table('permission_role')->updateOrInsert([
                'role_id' => $role->id,
                'permission_id' => $permId
            ]);
        }

        return response()->json(['success' => true, 'message' => '✅ Permissions assignées à admin']);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});


Route::get('/seed-admin-user', function () {
    try {
        $role = DB::table('roles')->where('name', 'admin')->first();

        DB::table('users')->updateOrInsert([
            'email' => 'admin@admin.com'
        ], [
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'firstConnexion' => false,
            'status' => 'actif',
            'role_id' => $role->id,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json(['success' => true, 'message' => '✅ Utilisateur admin créé']);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});

Route::get('/seed-types-conges', function () {
    try {
        $types = [
            ['libelle' => 'Annuel', 'jours_par_defaut' => 30],
            ['libelle' => 'Maternité', 'jours_par_defaut' => 90],
            ['libelle' => 'Maladie', 'jours_par_defaut' => 15],
        ];

        foreach ($types as $type) {
            DB::table('types_conges')->updateOrInsert(['libelle' => $type['libelle']], $type);
        }

        return response()->json(['success' => true, 'message' => '✅ Types de congé insérés']);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});

Route::get('/seed-types-agent', function () {
    try {
        $types = ['Contractuel', 'Fonctionnaire', 'Volontaire'];

        foreach ($types as $name) {
            DB::table('types_agent')->updateOrInsert(['name' => $name]);
        }

        return response()->json(['success' => true, 'message' => '✅ Types d\'agents insérés']);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});

Route::get('/seed-services', function () {
    try {
        $services = ['Informatique', 'RH', 'Comptabilité', 'Direction'];

        foreach ($services as $name) {
            DB::table('services')->updateOrInsert(['name' => $name]);
        }

        return response()->json(['success' => true, 'message' => '✅ Services insérés']);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});

Route::get('/seed-fonctions', function () {
    try {
        $fonctions = ['Développeur', 'Chef RH', 'Comptable', 'Directeur général'];

        foreach ($fonctions as $name) {
            DB::table('fonctions')->updateOrInsert(['name' => $name]);
        }

        return response()->json(['success' => true, 'message' => '✅ Fonctions insérées']);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});


Route::get('/seed-employes', function () {
    try {
        $fonctionId = DB::table('fonctions')->where('name', 'Développeur')->value('id');
        $serviceId = DB::table('services')->where('name', 'Informatique')->value('id');
        $typeAgentId = DB::table('types_agent')->where('name', 'Contractuel')->value('id');
        $userId = DB::table('users')->where('email', 'admin@admin.com')->value('id');

        DB::table('employes')->updateOrInsert([
            'email' => 'employe1@example.com'
        ], [
            'nom' => 'Doe',
            'prenom' => 'John',
            'adresse' => 'Dakar',
            'date_naiss' => '1990-01-01',
            'lieu_naiss' => 'Dakar',
            'situation_matrimoniale' => 'Célibataire',
            'date_prise_service' => '2020-01-01',
            'genre' => 'Masculin',
            'type_contrat' => 'CDI',
            'solde_conge_jours' => 20,
            'fonction_id' => $fonctionId,
            'service_id' => $serviceId,
            'type_agent_id' => $typeAgentId,
            'user_id' => $userId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => '✅ Employé inséré']);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});

Route::get('/seed-documents', function () {
    try {
        $employeId = DB::table('employes')->where('email', 'employe1@example.com')->value('id');

        DB::table('documents')->insertOrIgnore([
            'nom' => 'Contrat CDI',
            'fichier' => 'contrat_cdi.pdf',
            'description' => 'Contrat de travail de John Doe',
            'employe_id' => $employeId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => '✅ Document inséré']);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});


Route::get('/seed-conges', function () {
    try {
        $employeId = DB::table('employes')->where('email', 'employe1@example.com')->value('id');
        $typeCongeId = DB::table('types_conges')->where('libelle', 'Annuel')->value('id');

        DB::table('conges')->insertOrIgnore([
            'employe_id' => $employeId,
            'type_conge_id' => $typeCongeId,
            'date_demande' => now()->subDays(10),
            'date_debut' => now()->subDays(5),
            'date_fin' => now(),
            'nombre_jours' => 5,
            'statut' => 'approuve',
            'motif' => 'Repos annuel',
            'commentaire' => 'Aucun',
            'piece_jointe' => null,
            'note_pdf' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => '✅ Congé inséré']);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});


Route::get('/seed-cessations', function () {
    try {
        $congeId = DB::table('conges')->first()?->id;

        if (!$congeId) {
            return response()->json(['error' => 'Aucun congé trouvé']);
        }

        DB::table('cessations')->insertOrIgnore([
            'conge_id' => $congeId,
            'date_debut' => now()->subDays(2),
            'date_fin' => now(),
            'statut' => 'valide',
            'motif' => 'Fin de contrat temporaire',
            'commentaire' => 'RAS',
            'fiche_cessation_pdf' => 'fiche_test.pdf',
            'nombre_jours' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => '✅ Cessation insérée']);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});



//-----------------------------------------------------SEEDERS6------------------------------------------



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
        Route::middleware('auth:sanctum')->post("/traiterDemandeConge/{id}", [CongeController::class, 'traiter']);


        Route::middleware('auth:sanctum')->put("/{id}", [CongeController::class, 'update']);
        Route::middleware('auth:sanctum')->delete("/{id}", [congeController::class, 'destroy']);
    });

    //--------------- Cessations Routes ---------------
    Route::prefix('cessations')->group(function () {
        Route::middleware('auth:sanctum')->get("/", [CessationController::class, 'index']);
        Route::middleware('auth:sanctum')->get("/mesCessations", [CessationController::class, 'mesCessations']);
        Route::middleware('auth:sanctum')->get("/{id}", [CessationController::class, 'show']);

        Route::middleware('auth:sanctum')->post("/", [CessationController::class, 'store']);
        Route::middleware('auth:sanctum')->post("/traiterDemandeCessation/{id}", [CessationController::class, 'traiter']);


        Route::middleware('auth:sanctum')->put("/{id}", [CessationController::class, 'update']);
        Route::middleware('auth:sanctum')->delete("/{id}", [CessationController::class, 'destroy']);
    });
});
