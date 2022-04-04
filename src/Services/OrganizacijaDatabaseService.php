<?php

namespace App\Services;

use App\Entity\Organizacija;
use Doctrine\Persistence\ManagerRegistry;

class OrganizacijaDatabaseService {

    private $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry) {
        $this->managerRegistry = $managerRegistry;
    }

    public function findAll() {
        return $this->managerRegistry->getRepository(Organizacija::class)->findAll();
    }

    public function find($id) {
        return $this->managerRegistry->getRepository(Organizacija::class)->find($id);
    }

}