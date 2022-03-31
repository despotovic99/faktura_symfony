<?php

namespace App\Controller;

use App\Entity\Faktura;
use App\Entity\Organizacija;
use App\Entity\StavkaFakture;
use Doctrine\Persistence\ManagerRegistry;

class FakturaController {

    private $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry) {
        $this->managerRegistry = $managerRegistry;
    }

    public function findAll() {
        return $this->managerRegistry->getRepository(Faktura::class)->findAll();
    }

    public function find($id) {
        return $this->managerRegistry->getRepository(Faktura::class)->find($id);
    }

    public function save($fakturaObjekat) {
        $faktura = $this->managerRegistry->getRepository(Faktura::class)->find($fakturaObjekat['id']);
        if (!$faktura) {
            $faktura = new Faktura();
        }

        $entityManager = $this->managerRegistry->getManager();

        $this->managerRegistry->getConnection()->beginTransaction();
        try {
            $faktura->setBrojRacuna($fakturaObjekat['brojRacuna']);
            $faktura->setDatumIzdavanja(new \DateTime($fakturaObjekat['datumIzdavanja']));

            $organizacija = $entityManager->getRepository(Organizacija::class)->find($fakturaObjekat['organizacija']);

            $faktura->setOrganizacija($organizacija);

            $faktura->obrisiStavke();

            $entityManager->persist($faktura);
            $entityManager->flush();
            $this->managerRegistry->getConnection()->commit();
        } catch (\Throwable $e) {
            // todo dodaj  flash  ovde message
            $this->addFlash('poruka', 'Faktura nije sacuvana!');
            $this->managerRegistry->getConnection()->rollback();
            return $this->redirectToRoute('sve_fakture');
        }

        if (isset($fakturaObjekat['stavke'])) {
            foreach ($fakturaObjekat['stavke'] as $stavkaObjekat) {
                $stavka = $entityManager->getRepository(StavkaFakture::class)->find($stavkaObjekat['id']);
                if (!$stavka) {
                    $stavka = new StavkaFakture();
                }

                if ($stavka->getFaktura() != null && $stavka->getFaktura()->getId() != $faktura->getId()) {
                    // id fakture u stavci nije isti kao id fakture za koju treba da se veze
                    continue;
                }

                $this->managerRegistry->getConnection()->beginTransaction();
                try {
                    $stavka->setNazivArtikla($stavkaObjekat['naziv_artikla']);
                    $stavka->setKolicina($stavkaObjekat['kolicina']);

                    $faktura->addStavke($stavka);

                    $entityManager->persist($faktura);
                    $entityManager->flush();
                    $this->managerRegistry->getConnection()->commit();
                } catch (\Throwable $e) {
                    $this->addFlash('poruka', "Problem prilikom cuvanja stavke " . $stavka->getNazivArtikla());
                    $this->managerRegistry->getConnection()->rollback();
                }
            }
        }

    }

    public function delete(Faktura $faktura) {

        $this->managerRegistry->getConnection()->beginTransaction();
        try {
            $entityManager = $this->managerRegistry->getManager();
            $entityManager->remove($faktura);
            $entityManager->flush();
            $this->managerRegistry->getConnection()->commit();
        } catch (\Throwable $exception) {
            $this->managerRegistry->getConnection()->rollback();
            return false;
        }
        return true;
    }


}