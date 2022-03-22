<?php

namespace App\Entity;

use App\Repository\FakturaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: FakturaRepository::class)]
#[ORM\Table(name: 'fakture')]
#[UniqueEntity('broj_racuna')]
class Faktura
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 30)]
    #[Assert\NotBlank(message: 'Unesi broj racuna')]
    private $broj_racuna;

    #[ORM\Column(type: 'date')]
    #[Assert\NotBlank(message: 'Unesi datum izdavanja racuna')]
    private $datum_izdavanja;

    #[ORM\OneToMany(mappedBy: 'faktura', targetEntity: StavkaFakture::class, orphanRemoval: true)]
    private $stavke;

    #[ORM\ManyToOne(targetEntity: Organizacija::class, inversedBy: 'fakture')]
    #[ORM\JoinColumn(nullable: false)]
    private $organizacija;

    public function __construct()
    {
        $this->stavke = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBrojRacuna(): ?string
    {
        return $this->broj_racuna;
    }

    public function setBrojRacuna(string $broj_racuna): self
    {
        $this->broj_racuna = $broj_racuna;

        return $this;
    }

    public function getDatumIzdavanja(): ?\DateTimeInterface
    {
        return $this->datum_izdavanja;
    }

    public function setDatumIzdavanja(\DateTimeInterface $datum_izdavanja): self
    {
        $this->datum_izdavanja = $datum_izdavanja;

        return $this;
    }

    /**
     * @return Collection<int, StavkaFakture>
     */
    public function getStavke(): Collection
    {
        return $this->stavke;
    }

    public function addStavke(StavkaFakture $stavke): self
    {
        if (!$this->stavke->contains($stavke)) {
            $this->stavke[] = $stavke;
            $stavke->setFaktura($this);
        }

        return $this;
    }

    public function removeStavke(StavkaFakture $stavke): self
    {
        if ($this->stavke->removeElement($stavke)) {
            // set the owning side to null (unless already changed)
            if ($stavke->getFaktura() === $this) {
                $stavke->setFaktura(null);
            }
        }

        return $this;
    }

    public function getOrganizacija(): ?Organizacija
    {
        return $this->organizacija;
    }

    public function setOrganizacija(?Organizacija $organizacija): self
    {
        $this->organizacija = $organizacija;

        return $this;
    }
}
