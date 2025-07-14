<?php

namespace App\Services;

use App\Repositories\Statistics\CessationStatsProvider;
use App\Repositories\Statistics\CongeStatsProvider;
use App\Repositories\Statistics\EmployeStatsProvider;


class StatistiqueService
{
    protected array $providers;

    public function __construct()
    {
        $this->providers = [
            'conges' => new CongeStatsProvider(),
            'cessations' => new CessationStatsProvider(),
            'employes' => new EmployeStatsProvider(),
        ];
    }

    public function getAllStats(): array
    {
        $stats = [];

        foreach ($this->providers as $key => $provider) {
            $stats[$key] = $provider->getStats();
        }

        return $stats;
    }

    public function getStatsFor(string $entity): array
    {
        if (!isset($this->providers[$entity])) {
            throw new \InvalidArgumentException("Aucun provider pour $entity.");
        }

        return $this->providers[$entity]->getStats();
    }
}
