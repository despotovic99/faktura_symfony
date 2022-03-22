<?php

namespace App\DataFixtures;

use App\Entity\Faktura;
use App\Entity\Organizacija;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class FakturaFixtures extends Fixture implements  DependentFixtureInterface {
    public function load(ObjectManager $manager): void {

        foreach ($this->FakturaData() as [$broj_racuna, $datum_izdavanja, $organizacija_id]) {

            $organizacija = $manager->getRepository(Organizacija::class)->find($organizacija_id);
            $faktura = new Faktura();
            $faktura->setBrojRacuna($broj_racuna);
            $faktura->setDatumIzdavanja($datum_izdavanja);
            $faktura->setOrganizacija($organizacija);

            $manager->persist($faktura);

        }

        $manager->flush();
    }

    private function FakturaData() {
        return [
            ['a123', date_create('2020-03-02'), 1],
            ['b111', date_create('2020-05-05'), 1],
            ['c12jk3', date_create('2020-09-14'), 3],
            ['d1hk23', date_create('2020-11-29'), 2],
            ['e1sad23', date_create('2020-12-02'), 3],
            ['f12sa3', date_create('2020-09-23'), 1],
        ];
    }

    public function getDependencies() {
      return [
          OrganizacijaFixtures::class
      ];
    }
}
