<?php

namespace App\DataFixtures;

use App\Entity\JedinicaMere;
use App\Entity\Proizvod;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProizvodFixtures extends Fixture {
    public function load(ObjectManager $manager): void {

        foreach ($this->getListaProizvoda() as [$naziv, $cena, $jm]) {
            $jedinica = $manager->getRepository(JedinicaMere::class)->find($jm);
            $proizvod = new Proizvod();
            $proizvod->setNazivProizvoda($naziv);
            $proizvod->setCenaPoJedinici($cena);
            $proizvod->setJedinicaMere($jedinica);
         $manager->persist($proizvod);
        }

        $manager->flush();
    }

    private function getListaProizvoda() {
        return [
            ['Cement', 30, 1],
            ['Pesak', 15.2, 3],
            ['Drvo', 6, 3],
            ['Lepak', 10.1, 2],
            ['Stiropor', 20.1, 4],
            ['Sraf', 2, 5],
            ['Silikon', 2, 2],
        ];
    }

    public function getDependencies() {
        return [
            JedinicaMereFixtures::class
        ];
    }

}
