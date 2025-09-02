<?php

namespace App\Services;

use App\Repositories\JourExcluRepository;

class JourExcluService
{

    protected $jourExcluRepository;

    public function __construct(JourExcluRepository $jourExcluRepository)
    {
        $this->jourExcluRepository = $jourExcluRepository;
    }

    public function getAll()
    {
        return $this->jourExcluRepository->getAll();
    }

    public function getById(int $id)
    {
        return $this->jourExcluRepository->getById($id);
    }

    public function addJourExclu(array $data)
    {
        // Cas sécurité : si type_exclusion = unique => jour_semaine doit être NULL
        if ($data['type_exclusion'] === 'unique') {
            $data['jour_semaine'] = null;
        }

        // Si type_exclusion = recurrent => date doit être NULL
        if ($data['type_exclusion'] === 'recurrent') {
            $data['date'] = null;
        }
        return $this->jourExcluRepository->store($data);
    }

    public function updateJourExclu(int $id, array $data)
    {
        if ($data['type_exclusion'] === 'unique') {
            $data['jour_semaine'] = null;
        }

        if ($data['type_exclusion'] === 'recurrent') {
            $data['date'] = null;
        }

        return $this->jourExcluRepository->update($id, $data);
    }

    public function deleteJourExclu(int $id)
    {
        return $this->jourExcluRepository->destroy($id);
    }
}
