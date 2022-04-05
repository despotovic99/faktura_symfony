<?php

namespace App\DataFixtures;

use App\Entity\JedinicaMere;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class JedinicaMereFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        foreach ($this->getListaJedinicaMere() as [$naziv,$oznaka]){
         $jm = new JedinicaMere();
         $jm->setNaziv($naziv);
         $jm->setOznaka($oznaka);
         $manager->persist($jm);
        }
        $manager->flush();
    }

    private function getListaJedinicaMere(){
        return [
            ['kilogram','kg'],
            ['litar','l'],
            ['metar kubni','m3'],
            ['metar kvadratni','m2'],
            ['komad','pcs'],
        ];
    }

}

