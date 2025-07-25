<?php

namespace App\Providers;

use App\Interfaces\CessationInterface;
use App\Interfaces\CongesInterface;
use App\Interfaces\DisponibiliteInterface;
use App\Interfaces\DocumentInterface;
use App\Interfaces\EmployeInterface;
use App\Interfaces\TypesCongesInterface;
use App\Interfaces\UserInterface;
use App\Repositories\CessationRepository;
use App\Repositories\CongeRepository;
use App\Repositories\EmployeRepository;
use App\Repositories\TypesCongesRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;
use App\Models\PersonalAccessToken;
use App\Repositories\DisponibiliteRepository;
use App\Repositories\DocumentRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserInterface::class, UserRepository::class);
        $this->app->bind(EmployeInterface::class, EmployeRepository::class);
        $this->app->bind(CongesInterface::class, CongeRepository::class);
        $this->app->bind(CessationInterface::class, CessationRepository::class);
        $this->app->bind(TypesCongesInterface::class, TypesCongesRepository::class);
        $this->app->bind(DisponibiliteInterface::class, DisponibiliteRepository::class);
        $this->app->bind(DocumentInterface::class, DocumentRepository::class);
    }

    /**
     * Bootstrap any application services.  
     */
    public function boot(): void
    {
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
    }
}
