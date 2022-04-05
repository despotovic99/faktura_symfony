<?php

namespace App\Entity;

use App\Repository\JedinicaMereRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JedinicaMereRepository::class)]
class JedinicaMere
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 50)]
    private $naziv;

    #[ORM\Column(type: 'string', length: 20)]
    private $oznaka;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNaziv(): ?string
    {
        return $this->naziv;
    }

    public function setNaziv(string $naziv): self
    {
        $this->naziv = $naziv;

        return $this;
    }

    public function getOznaka(): ?string
    {
        return $this->oznaka;
    }

    public function setOznaka(string $oznaka): self
    {
        $this->oznaka = $oznaka;

        return $this;
    }
}
