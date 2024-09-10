<?php

namespace App\Services;

use App\Repository\DetteRepository;
use App\Repository\DetteRepositoryImp;

class DetteServiceImp implements DetteService{


    

    protected $detteRepository;

    public function __construct(DetteRepositoryImp $detteRepository)
    {
        $this->detteRepository = $detteRepository;
    }

    public function getPaiements(string $detteId)
    {
        return $this->detteRepository->getPaiements($detteId);
    }

    public function addPaiement(string $detteId, array $paiementData)
    {
        return $this->detteRepository->addPaiement($detteId, $paiementData);
    }
    public function getAll(){
        return $this->detteRepository->getAll();
    }
    public function getById(string $id)
    {
        return $this->detteRepository->getById($id);
    }
    public function create(array $data)
    {
        return $this->detteRepository->create($data);
    }
    public function update(string $id, array $data){

    }
    public function delete(string $id){

    }

    public function getArticles(string $detteId)
    {
        return $this->detteRepository->getArticles($detteId);
    }

    public function getAllFiltered($statut = null)
    {
        return $this->detteRepository->getAllFiltered($statut);
    }

    
}