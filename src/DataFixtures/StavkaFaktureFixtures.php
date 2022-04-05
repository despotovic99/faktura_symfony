<?php

namespace App\DataFixtures;

use App\Entity\Faktura;
use App\Entity\Proizvod;
use App\Entity\StavkaFakture;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class StavkaFaktureFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        foreach ($this->StavkaFaktureData() as [$proizvodId,$kolicina,$faktura_id]){

            $faktura = $manager->getRepository(Faktura::class)->find($faktura_id);
            $proizvod = $manager->getRepository(Proizvod::class)->find($proizvodId);
            $stavkaFakture=new StavkaFakture();
            $stavkaFakture->setProizvod($proizvod);
            $stavkaFakture->setKolicina($kolicina);
            $stavkaFakture->setFaktura($faktura);

            $manager->persist($stavkaFakture);

        }

        $manager->flush();
    }
    private function StavkaFaktureData() {
        return [
            [1, 5, 1],
            [2, 3, 1],
            [5, 1, 1],
            [5, 1, 2],
            [4, 1, 2],
            [3, 5, 2]
        ];
    }

    public function getDependencies() {
        return [
            FakturaFixtures::class,
            ProizvodFixtures::class
        ];
    }
}
