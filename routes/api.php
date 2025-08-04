<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CessationController;
use App\Http\Controllers\CongeController;
use App\Http\Controllers\DisponibiliteController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\EmployesController;
use App\Http\Controllers\PermissionRoleController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\StatistiqueController;
use App\Http\Controllers\TypesCongesController;
use App\Http\Controllers\UserController;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use App\Http\Middleware\ExpireInactiveToken;


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
        $permissions = [
            'lister-utilisateurs',
            'creer-compte-employe',
            'modifier-utilisateur',
            'modifer-utilisateur',
            'supprimer-utilisateur',
            'creer-type-contrat',
            'creer-role',
            'attribuer-role',
            'retirer-role',
            'creer-service',
            'modifier-service',
            'supprimer-service',
            'affecter-chef-service',
            'creer-fonction',
            'modifier-fonction',
            'supprimer-fonction',
            'lister-employes',
            'ajouter-employe',
            'modifier-employe',
            'supprimer-employe',
            'lister-conges',
            'traiter-demande-conge',
            'modifier-demande-conge',
            'supprimer-demande-conge',
            'lister-cessations',
            'traiter-demande-cessations',
            'modifier-demande-cessations',
            'supprimer-demande-cessations',
            'traiter-demande-permission',
            'lister-conges',
            'lister-cessations',
            'faire-demande-conge',
            'faire-demande-cessation',
        ];

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
        // Structure rôle => [permissions]
        $rolesWithPermissions = [
            'admin' => [
                //-----PARTIE UTILISATEURS
                'lister-utilisateurs',
                'creer-compte-employe',
                'modifier-utilisateur',
                'modifer-utilisateur',
                'supprimer-utilisateur',
                //----- CONTRATS
                'creer-type-contrat',
                //----- ROLES
                'creer-role',
                'attribuer-role',
                'retirer-role',
                //----- SERVICES
                'creer-service',
                'modifier-service',
                'supprimer-service',
                'affecter-chef-service',
                //----- FONCTIONS
                'creer-fonction',
                'modifier-fonction',
                'supprimer-fonction'
            ],
            'rh' => [
                //----- EMPLOYES
                'lister-employes',
                'ajouter-employe',
                'modifier-employe',
                'supprimer-employe',
                //----- CONGES
                'lister-conges',
                'traiter-demande-conge',
                'modifier-demande-conge',
                'supprimer-demande-conge',
                //----- CESSATIONS
                'lister-cessations',
                'traiter-demande-cessations',
                'modifier-demande-cessations',
                'supprimer-demande-cessations',
                //----- PERMISSION
                'traiter-demande-permission'
            ],
            'employe' => [
                'lister-conges',
                'lister-cessations',
                'faire-demande-conge',
                'faire-demande-cessation',
            ],
        ];

        foreach ($rolesWithPermissions as $roleName => $permissionNames) {
            $role = DB::table('roles')->where('name', $roleName)->first();

            if (!$role) {
                continue; // Ignorer si le rôle n'existe pas
            }

            foreach ($permissionNames as $permName) {
                $permission = DB::table('permissions')->where('name', $permName)->first();

                if ($permission) {
                    DB::table('permission_role')->updateOrInsert([
                        'role_id' => $role->id,
                        'permission_id' => $permission->id
                    ]);
                }
            }
        }

        return response()->json(['success' => true, 'message' => '✅ Permissions assignées aux rôles']);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});



Route::get('/seed-users', function () {
    try {
        // Structure: rôle => email
        $usersToCreate = [
            'rh'      => 'rh@rh.com',
            'employe' => 'employe@employe.com',
            'directeur'   => 'direc@direc.com',
        ];

        foreach ($usersToCreate as $roleName => $email) {
            $role = DB::table('roles')->where('name', $roleName)->first();

            if (!$role) {
                continue; // Ignorer si le rôle n'existe pas
            }

            DB::table('users')->updateOrInsert([
                'email' => $email
            ], [
                'password' => Hash::make('password123'), // même mot de passe pour tous au départ
                'email_verified_at' => now(),
                'firstConnexion' => false,
                'status' => 'actif',
                'role_id' => $role->id,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        return response()->json(['success' => true, 'message' => '✅ Utilisateurs avec rôles créés']);
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
        // Liste des employés à insérer
        $employes = [
            [
                'email' => 'employe@employe.com',
                'nom' => 'Ndiaye',
                'prenom' => 'Baba',
                'adresse' => 'Dakar',
                'date_naiss' => '1990-01-01',
                'lieu_naiss' => 'Dakar',
                'situation_matrimoniale' => 'Célibataire',
                'date_prise_service' => '2020-01-01',
                'genre' => 'Masculin',
                'type_contrat' => 'CDI',
                'solde_conge_jours' => 20,
                'user_email' => 'employe@employe.com',
                'fonction_name' => 'Développeur',
                'service_name' => 'Informatique',
                'type_agent_name' => 'Contractuel',
            ],
        ];

        foreach ($employes as $emp) {
            $fonctionId = DB::table('fonctions')->where('name', $emp['fonction_name'])->value('id');
            $serviceId = DB::table('services')->where('name', $emp['service_name'])->value('id');
            $typeAgentId = DB::table('types_agent')->where('name', $emp['type_agent_name'])->value('id');
            $userId = DB::table('users')->where('email', $emp['user_email'])->value('id');

            if (!$fonctionId || !$serviceId || !$typeAgentId || !$userId) {
                continue; // Ignorer si une référence n'existe pas
            }

            DB::table('employes')->updateOrInsert([
                'email' => $emp['email']
            ], [
                'nom' => $emp['nom'],
                'prenom' => $emp['prenom'],
                'adresse' => $emp['adresse'],
                'date_naiss' => $emp['date_naiss'],
                'lieu_naiss' => $emp['lieu_naiss'],
                'situation_matrimoniale' => $emp['situation_matrimoniale'],
                'date_prise_service' => $emp['date_prise_service'],
                'genre' => $emp['genre'],
                'type_contrat' => $emp['type_contrat'],
                'solde_conge_jours' => $emp['solde_conge_jours'],
                'fonction_id' => $fonctionId,
                'service_id' => $serviceId,
                'type_agent_id' => $typeAgentId,
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return response()->json(['success' => true, 'message' => '✅ Employés insérés avec succès']);
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
        Route::middleware('auth.expirable')->get("/", [ServiceController::class, 'index']);
        Route::get("/addChefService/{id}", [ServiceController::class, 'addChefService']);
        Route::middleware('auth.expirable')->get("/{id}", [ServiceController::class, 'show']);
        // Route::middleware('auth.expirable')->get("/addChefService/{id}", [ServiceController::class, 'addChefService']);
        Route::middleware('auth.expirable')->post("/", [ServiceController::class, 'store']);
    });
    //--------------- FONCTIONS Routes ---------------
    Route::prefix('fonctions')->group(function () {});

    //--------------- TYPES CONGES Routes ---------------
    Route::prefix('typesConges')->group(function () {
        Route::get("/", [TypesCongesController::class, 'index']);
        Route::get("/{id}", [TypesCongesController::class, 'show']);
        Route::post("/", [TypesCongesController::class, 'store']);
    });

    //--------------- STATISTICS Routes ---------------
    Route::prefix('stats')->group(function () {
        Route::get('/', [StatistiqueController::class, 'index']); // Toutes les stats
        Route::get('/{entity}', [StatistiqueController::class, 'show']); // Stats ciblées
    });


    //--------------- DOCUMENTS Routes ---------------
    Route::prefix('documents')->group(function () {
        Route::get('/', [DocumentController::class, 'index']);
        Route::get('/typeDocument', [DocumentController::class, 'allTypeDocument']);
        Route::post('/', [DocumentController::class, 'store']);
        Route::post('/typeDocument', [DocumentController::class, 'createDocumentType']);
    });
}); 

// components fermé avant tags


// Route::prefix('v1')->group(function () {
//     //--------------- Permissions and Roles Routes ---------------
//     Route::prefix('permissions')->group(function () {
//         Route::get('/roles-permissions', [PermissionRoleController::class, 'index']);
//     });

//     //--------------- Authentication Routes ---------------
//     Route::prefix('auth')->group(function () {
//         Route::post('/login', [AuthController::class, 'login']);
//         Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
//         Route::middleware('auth:sanctum')->get("/connectedUser", [AuthController::class, 'connectedUser']);
//     });

//     //--------------- Users Routes ---------------
//     Route::prefix('users')->group(function () {
//         Route::middleware('auth:sanctum')->get("/all", [UserController::class, 'index']);
//         Route::middleware('auth:sanctum')->post('/create-employe-account', [UserController::class, 'createUserForEmploye']);
//         Route::middleware('auth:sanctum')->get("/{id}", [UserController::class, 'show']);
//         Route::middleware('auth:sanctum')->put("/{id}", [UserController::class, 'update']);
//         Route::middleware('auth:sanctum')->delete("/{id}", [UserController::class, 'destroy']);
//         Route::middleware(['auth:sanctum'])->post('/change-password', [UserController::class, 'changePassword']);
//         Route::post('/change-password', [UserController::class, 'changePassword']);
//     });

//     //--------------- Employes Routes ---------------
//     Route::prefix('employes')->group(function () {
//         // Route::middleware('auth:sanctum')->get("/", [EmployesController::class, 'index']);
//         Route::middleware(['auth.expirable'])->get("/", [EmployesController::class, 'index']);
//         Route::middleware('auth:sanctum')->get("/{id}", [EmployesController::class, 'show']);
//         Route::middleware('auth:sanctum')->post("/", [EmployesController::class, 'store']);
//         Route::middleware('auth:sanctum')->put("/{id}", [EmployesController::class, 'update']);
//     });

//     //--------------- Conges Routes ---------------
//     Route::prefix('conges')->group(function () {
//         // Route::middleware('auth:sanctum')->get("/", [CongeController::class, 'index']);
//         Route::middleware(['auth.expirable'])->get("/", [CongeController::class, 'index']);
//         Route::middleware('auth:sanctum')->get("/mesConges", [CongeController::class, 'mesConges']);
//         Route::middleware('auth:sanctum')->get("/{id}", [CongeController::class, 'show']);

//         Route::middleware('auth:sanctum')->post("/", [CongeController::class, 'store']);
//         Route::middleware('auth:sanctum')->post("/traiterDemandeConge/{id}", [CongeController::class, 'traiter']);


//         Route::middleware('auth:sanctum')->put("/{id}", [CongeController::class, 'update']);
//         Route::middleware('auth:sanctum')->delete("/{id}", [congeController::class, 'destroy']);
//     });

//     //--------------- Cessations Routes ---------------
//     Route::prefix('cessations')->group(function () {
//         Route::middleware('auth:sanctum')->get("/", [CessationController::class, 'index']);
//         Route::middleware('auth:sanctum')->get("/mesCessations", [CessationController::class, 'mesCessations']);
//         Route::middleware('auth:sanctum')->get("/{id}", [CessationController::class, 'show']);

//         Route::middleware('auth:sanctum')->post("/", [CessationController::class, 'store']);
//         Route::middleware('auth:sanctum')->post("/traiterDemandeCessation/{id}", [CessationController::class, 'traiter']);


//         Route::middleware('auth:sanctum')->put("/{id}", [CessationController::class, 'update']);
//         Route::middleware('auth:sanctum')->delete("/{id}", [CessationController::class, 'destroy']);
//     });

//     //--------------- SERVICES Routes ---------------
//     Route::prefix('services')->group(function () {});
//     //--------------- FONCTIONS Routes ---------------
//     Route::prefix('fonctions')->group(function () {});

//     //--------------- TYPES CONGES Routes ---------------
//     Route::prefix('typesConges')->group(function () {
//         Route::get("/", [TypesCongesController::class, 'index']);
//         Route::get("/{id}", [TypesCongesController::class, 'show']);
//         Route::post("/", [TypesCongesController::class, 'store']);
//     });

//     //--------------- STATISTICS Routes ---------------
//     Route::prefix('stats')->group(function () {
//         Route::get('/', [StatistiqueController::class, 'index']); // Toutes les stats
//         Route::get('/{entity}', [StatistiqueController::class, 'show']); // Stats ciblées
//     });
// });

/*
1: Mettre à jour les routes en utilisant auth.expirable à la place de sanctum
2: Push des modifs
3: Tester avec le front
*/ 
// Pas de motif pour le congé