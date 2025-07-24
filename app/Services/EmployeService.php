<?php

namespace App\Services;

use App\Models\Employe;
use App\Repositories\EmployeRepository;
use Carbon\Carbon;

class EmployeService
{
    protected $employeRepository;

    public function __construct(EmployeRepository $employeRepository)
    {
        $this->employeRepository = $employeRepository;
    }

    public function getAllEmployes()
    {
        return $this->employeRepository->getAllEmployes();
    }

    public function getEmployeById(int $id)
    {
        return $this->employeRepository->getEmployeById($id);
    }

    public function createEmploye(array $data)
    {
        if (
            in_array($data['type_contrat'], ['CDD', 'Intérim', 'Stage', 'Apprentissage']) &&
            !empty($data['duree_contrat_mois'])
        ) {
            $datePriseService = Carbon::parse($data['date_prise_service']);
            $dateFin = $datePriseService->addMonths($data['duree_contrat_mois']);
            $data['date_fin_contrat'] = $dateFin->toDateString();
            $data['duree_contrat_mois'] = $data['duree_contrat_mois'];
        }

        return $this->employeRepository->createEmploye($data);
    }

    public function updateEmploye(int $id, array $data)
    {
        return $this->employeRepository->updateEmploye($id, $data);
    }

    public function deleteEmploye(int $id)
    {
        return $this->employeRepository->deleteEmploye($id);
    }

    public function getEmployeByEmail(string $email)
    {
        return $this->employeRepository->getEmployeByEmail($email);
    }

    public function buildDossier(Employe $employe)
    {
        // $user = $employe->user;

        return  [
            'dossier' => [

                'infos_perso' => [
                    'nom' => $employe->nom,
                    'prenom' => $employe->prenom,
                    'emaim' => $employe->email,
                    'adresse' => $employe->adresse,
                    'date_naissance' => $employe->date_naiss,
                    'lieu_naissance' => $employe->lieu_naiss,
                    'situation_matrimoniale' => $employe->situation_matrimoniale,
                    'genre' => $employe->genre,
                ],

                'infos_profess' => [
                    'date_prise_service' => $employe->date_prise_service,
                    'type_contrat' => $employe->type_contrat,
                    'fonction' => $employe->fonction?->name,
                    'service' => $employe->service?->name,
                    'type_agent' => $employe->typeAgent?->name
                ],

                'gestion_absences' => [
                    'conges' => $employe->conges,
                    'cessations' => $employe->cessations,
                    'disponibilites' => $employe->disponibilites
                ],

                'documents_admin' => [
                    'documents' => $employe->documents
                ]
            ]
        ];
    }
}
