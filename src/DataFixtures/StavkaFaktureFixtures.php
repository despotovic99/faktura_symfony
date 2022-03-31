<?php

namespace App\DataFixtures;

use App\Entity\Faktura;
use App\Entity\StavkaFakture;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class StavkaFaktureFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        foreach ($this->StavkaFaktureData() as [$naziv_artikla,$kolicina,$faktura_id]){

            $faktura = $manager->getRepository(Faktura::class)->find($faktura_id);
            $stavkaFakture=new StavkaFakture();
            $stavkaFakture->setNazivArtikla($naziv_artikla);
            $stavkaFakture->setKolicina($kolicina);
            $stavkaFakture->setFaktura($faktura);

            $manager->persist($stavkaFakture);

        }

        $manager->flush();
    }
    private function StavkaFaktureData() {
        return [
            ['Artikal 1', 5, 1],
            ['Artikal 2', 3, 1],
            ['Artikal 3', 1, 1],
            ['Artikal 1', 5, 2],
            ['Artikal 2', 5, 2],
            ['Artikal 3', 5, 2],
            ['Artikal 1', 77, 3],
            ['Artikal 2', 11, 3],
            ['Artikal 3', 41, 3],
            ['Artikal 1', 35, 4],
            ['Artikal 2', 12, 4],
            ['Artikal 3', 4, 4],
            ['Artikal 1', 54, 5],
            ['Artikal 2', 1, 6],
            ['Artikal 3', 2, 6],

        ];
    }

    public function getDependencies() {
        return [
            FakturaFixtures::class
        ];
    }
}
