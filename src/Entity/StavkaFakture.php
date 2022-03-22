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

    #[ORM\Column(type: 'string', length: 255)]
    private $naziv_artikla;

    #[ORM\Column(type: 'integer')]
    private $kolicina;

    #[ORM\ManyToOne(targetEntity: Faktura::class, inversedBy: 'stavke')]
    #[ORM\JoinColumn(nullable: false)]
    private $faktura;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNazivArtikla(): ?string
    {
        return $this->naziv_artikla;
    }

    public function setNazivArtikla(string $naziv_artikla): self
    {
        $this->naziv_artikla = $naziv_artikla;

        return $this;
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
}
