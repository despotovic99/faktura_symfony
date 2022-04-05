<?php

namespace App\Entity;

use App\Repository\StavkaFaktureRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StavkaFaktureRepository::class)]
class StavkaFakture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Proizvod::class, inversedBy: 'stavkaFaktures')]
    #[ORM\JoinColumn(nullable: false)]
    private $proizvod;

    #[ORM\Column(type: 'integer')]
    private $kolicina;

    #[ORM\ManyToOne(targetEntity: Faktura::class, inversedBy: 'stavke')]
    #[ORM\JoinColumn(nullable: false)]
    private $faktura;


    public function getId(): ?int
    {
        return $this->id;
    }


    public function getKolicina(): ?int
    {
        return $this->kolicina;
    }

    public function setKolicina(int $kolicina): self
    {
        $this->kolicina = $kolicina;

        return $this;
    }

    public function getFaktura(): ?Faktura
    {
        return $this->faktura;
    }

    public function setFaktura(?Faktura $faktura): self
    {
        $this->faktura = $faktura;

        return $this;
    }

    public function getProizvod(): ?Proizvod
    {
        return $this->proizvod;
    }

    public function setProizvod(?Proizvod $proizvod): self
    {
        $this->proizvod = $proizvod;

        return $this;
    }
}
