<?php

namespace App\Services;

use App\Entity\Faktura;
use App\Entity\Organizacija;
use App\Entity\StavkaFakture;
use Doctrine\Persistence\ManagerRegistry;

class FakturaDatabaseService {

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

    public function save($fakturaSaForme) {
        $faktura = $this->find($fakturaSaForme['id']);
        if (!$faktura) {
            $faktura = new Faktura();
        }
        $entityManager = $this->managerRegistry->getManager();

        if (isset($fakturaSaForme['stavke']) && !$this->proveraStavki($faktura, $fakturaSaForme['stavke'])) {
            return 'Fatal error!';
        }

        $this->managerRegistry->getConnection()->beginTransaction();
        try {
            $faktura->setBrojRacuna($fakturaSaForme['brojRacuna']);
            $faktura->setDatumIzdavanja(new \DateTime($fakturaSaForme['datumIzdavanja']));

            $organizacija = $entityManager->getRepository(Organizacija::class)->find($fakturaSaForme['organizacija']);
            $faktura->setOrganizacija($organizacija);

            if (!isset($fakturaSaForme['stavke'])) {
                $faktura->obrisiStavke();
            }

            $entityManager->persist($faktura);
            $entityManager->flush();
            $this->managerRegistry->getConnection()->commit();
        } catch (\Throwable $exception) {
            $this->managerRegistry->getConnection()->rollback();
            return 'Neuspesno sacuvana faktura';
        }

        if (isset($fakturaSaForme['stavke'])) {
            $stavkeSaForme = $fakturaSaForme['stavke'];
            uasort($stavkeSaForme, [$this, 'cmp']);

            $stavkeIzBaze = $this->managerRegistry->getRepository(StavkaFakture::class)
                ->findBy(['faktura' => $faktura->getId()], ['id' => 'ASC']);

            $duzina = count($stavkeIzBaze);
            if ($duzina < count($stavkeSaForme)) {
                $duzina = count($stavkeSaForme);
            }

            $pomocnaStavkaForma = array_shift($stavkeSaForme);
            $pomocnaStavkaBaza = array_shift($stavkeIzBaze);

            for ($i = 0; $i <= $duzina; $i++) {
                $this->managerRegistry->getConnection()->beginTransaction();
                try {
                    if ($pomocnaStavkaForma != null && $pomocnaStavkaForma['id'] == '') {
                        $pomocnaNova = $pomocnaStavkaForma;
                        $pomocnaStavkaForma = array_shift($stavkeSaForme);

                        $novaStavka = new StavkaFakture();
                        $novaStavka->setNazivArtikla($pomocnaNova['naziv_artikla']);
                        $novaStavka->setKolicina($pomocnaNova['kolicina']);
                        $novaStavka->setFaktura($faktura);


                        $entityManager->persist($novaStavka);
                    } elseif ($pomocnaStavkaForma == null ||
                        ($pomocnaStavkaBaza != null && $pomocnaStavkaForma['id'] > $pomocnaStavkaBaza->getId())) {

                        $stavka = $pomocnaStavkaBaza;

                        $pomocnaStavkaBaza = array_shift($stavkeIzBaze);

                        $entityManager->remove($stavka);

                    } elseif ($pomocnaStavkaForma != null &&
                        $pomocnaStavkaBaza != null &&
                        $pomocnaStavkaForma['id'] == $pomocnaStavkaBaza->getId()) {

                        $stavkaSaVrednostima = $pomocnaStavkaForma;
                        $stavka = $pomocnaStavkaBaza;

                        $pomocnaStavkaForma = array_shift($stavkeSaForme);
                        $pomocnaStavkaBaza = array_shift($stavkeIzBaze);

                        $stavka->setNazivArtikla($stavkaSaVrednostima['naziv_artikla']);
                        $stavka->setKolicina($stavkaSaVrednostima['kolicina']);

                        $entityManager->persist($stavka);
                    }
                    $entityManager->flush();
                    $this->managerRegistry->getConnection()->commit();
                } catch (\Throwable $exception) {
                    $this->managerRegistry->getConnection()->rollback();
                }
            }
        }

        return 'Uspesno sacuvana faktura';
    }

    private function proveraStavki($faktura, $stavke): bool {
        foreach ($stavke as $stavka) {
            $stavkaBaza = $this->managerRegistry->getRepository(StavkaFakture::class)->find($stavka['id']);
            if ((!$stavkaBaza && !empty($stavka['id'])) ||
                $stavkaBaza &&
                $stavkaBaza->getFaktura() != null &&
                $stavkaBaza->getFaktura()->getId() != $faktura->getId()) {
                return false;
            }
        }
        return true;
    }

    function cmp($a, $b) {
        if ($a['id'] == $b['id']) {
            return 0;
        }
        return ($a['id'] < $b['id']) ? -1 : 1;
    }
}
