<?php

namespace App\Services;

use App\Entity\Proizvod;
use Doctrine\Persistence\ManagerRegistry;

class ProizvodDatabaseService {
    private $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry) {
        $this->managerRegistry = $managerRegistry;
    }

    public function findAll() {
        return $this->managerRegistry->getRepository(Proizvod::class)->findAll();
    }

    public function find($id) {
        return $this->managerRegistry->getRepository(Proizvod::class)->find($id);
    }
}