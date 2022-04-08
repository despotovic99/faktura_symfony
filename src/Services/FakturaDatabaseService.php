<?php

namespace App\Services;

use App\Entity\Faktura;
use App\Entity\Organizacija;
use App\Entity\Proizvod;
use App\Entity\StavkaFakture;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class FakturaDatabaseService {

    private $managerRegistry;
    private $validator;

    public function __construct(ManagerRegistry $managerRegistry,ValidatorInterface $validator) {
        $this->managerRegistry = $managerRegistry;
        $this->validator=$validator;
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
            return [
                'Fatal error!',
                $faktura->getId()
            ];
        }

        $this->managerRegistry->getConnection()->beginTransaction();
        try {
            $faktura->setBrojRacuna($fakturaSaForme['broj_racuna']);
            $faktura->setDatumIzdavanja(new \DateTime($fakturaSaForme['datum_izdavanja']));

            $organizacija = $entityManager->getRepository(Organizacija::class)->find($fakturaSaForme['organizacija']);
            $faktura->setOrganizacija($organizacija);

            $errors = $this->validator->validate($faktura);
            if(count($errors)>0){
                throw new \Exception('Neispravno popunjena faktura');
            }

            if (!isset($fakturaSaForme['stavke'])) {
                $faktura->obrisiStavke();
            }

            $entityManager->persist($faktura);
            $entityManager->flush();
            $this->managerRegistry->getConnection()->commit();
        } catch (\Throwable $exception) {
            $this->managerRegistry->getConnection()->rollback();
            return [
                $exception->getMessage(),
                $faktura->getId()
            ];
        }

        if (isset($fakturaSaForme['stavke'])) {
            $stavkeSaForme = $fakturaSaForme['stavke'];
            uasort($stavkeSaForme, [$this, 'uporediElemente']);

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
                    if ($pomocnaStavkaForma != null && $pomocnaStavkaForma['stavka_id'] == '') {
                        $pomocnaNova = $pomocnaStavkaForma;
                        $pomocnaStavkaForma = array_shift($stavkeSaForme);

                        $novaStavka = new StavkaFakture();
                        $proizvod = $this->managerRegistry->getRepository(Proizvod::class)
                            ->find($pomocnaNova['stavka_proizvod']);
                        $novaStavka->setProizvod($proizvod);
                        $novaStavka->setKolicina($pomocnaNova['kolicina']);
                        $novaStavka->setFaktura($faktura);


                        $entityManager->persist($novaStavka);
                    } elseif ($pomocnaStavkaForma == null ||
                        ($pomocnaStavkaBaza != null && $pomocnaStavkaForma['stavka_id'] > $pomocnaStavkaBaza->getId())) {

                        $stavka = $pomocnaStavkaBaza;

                        $pomocnaStavkaBaza = array_shift($stavkeIzBaze);

                        $entityManager->remove($stavka);

                    } elseif ($pomocnaStavkaForma != null &&
                        $pomocnaStavkaBaza != null &&
                        $pomocnaStavkaForma['stavka_id'] == $pomocnaStavkaBaza->getId()) {

                        $stavkaSaVrednostima = $pomocnaStavkaForma;
                        $stavka = $pomocnaStavkaBaza;

                        $pomocnaStavkaForma = array_shift($stavkeSaForme);
                        $pomocnaStavkaBaza = array_shift($stavkeIzBaze);

                        $proizvod = $this->managerRegistry->getRepository(Proizvod::class)->find($stavkaSaVrednostima['stavka_proizvod']);
                        $stavka->setProizvod($proizvod);
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

        return [
            'Uspesno sacuvana faktura',
            $faktura->getId()
        ];
    }

    private function proveraStavki($faktura, $stavke): bool {
        foreach ($stavke as $stavka) {
            $stavkaBaza = $this->managerRegistry->getRepository(StavkaFakture::class)->find($stavka['stavka_id']);
            if ((!$stavkaBaza && !empty($stavka['stavka_id'])) ||
                $stavkaBaza &&
                $stavkaBaza->getFaktura() != null &&
                $stavkaBaza->getFaktura()->getId() != $faktura->getId()) {
                return false;
            }
        }
        return true;
    }

    function uporediElemente($a, $b) {
        if ($a['stavka_id'] == $b['stavka_id']) {
            return 0;
        }
        return ($a['stavka_id'] < $b['stavka_id']) ? -1 : 1;
    }

    private function fakturaNotValid($fakturaSaForme):bool{
        return empty($fakturaSaForme['broj_racuna']) ||
                      empty($fakturaSaForme['datum_izdavanja']) ||
                      empty($fakturaSaForme['organizacija']) ;
    }
}
