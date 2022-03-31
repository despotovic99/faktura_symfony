<?php

namespace App\DataFixtures;

use App\Entity\Organizacija;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class OrganizacijaFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        for($i=1 ; $i<=3 ; $i++){

            $organizacija= new Organizacija();
            $organizacija->setNaziv("Organizacija $i" );

            $manager->persist($organizacija);
        }
        $manager->flush();
    }
}
