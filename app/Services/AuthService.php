<?php

namespace App\Services;
use Illuminate\Support\Env;
class AuthService
{
    protected $mysqlService;
    protected $postgresService;

    public function __construct(MysqlService $mysqlService, PostgresService $postgresService)
    {
        $this->mysqlService = $mysqlService;
        $this->postgresService = $postgresService;
    }

    public function login(array $credentials)
    {
        $dbService = env('DATABASE_SERVICE', 'mysql');

        return match ($dbService) {
            'postgres' => $this->postgresService->login($credentials),
            'mysql' => $this->mysqlService->login($credentials),
            default => throw new \Exception("Service d'authentification [$dbService] non supporté."),
        };
    }
}
