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
#[UniqueEntity('brojRacuna')]
class Faktura {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 30, name: 'broj_racuna',unique: true)]
    #[Assert\NotBlank(message: 'Unesi broj racuna',allowNull: false)]
    private $brojRacuna;

    #[ORM\Column(type: 'date',name: 'datum_izdavanja')]
    #[Assert\NotBlank(message: 'Unesi datum izdavanja racuna',allowNull: false)]
    private $datumIzdavanja;

    #[ORM\ManyToOne(targetEntity: Organizacija::class, inversedBy: 'fakture')]
    #[Assert\NotNull(message: 'Organizacija nije odabrana')]
    private $organizacija;

    #[ORM\OneToMany(mappedBy: 'faktura', targetEntity: StavkaFakture::class, orphanRemoval: true, cascade: ['persist'])]
    private $stavke;


    public function __construct() {
        $this->stavke = new ArrayCollection();
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getBrojRacuna(): ?string {
        return $this->brojRacuna;
    }

    public function setBrojRacuna(string $brojRacuna): self {
        $this->brojRacuna = $brojRacuna;

        return $this;
    }

    public function getDatumIzdavanja(): ?\DateTimeInterface {
        return $this->datumIzdavanja;
    }

    public function setDatumIzdavanja(\DateTimeInterface $datumIzdavanja): self {
        $this->datumIzdavanja = $datumIzdavanja;

        return $this;
    }

    /**
     * @return Collection<int, StavkaFakture>
     */
    public function getStavke(): Collection {
        return $this->stavke;
    }

    public function obrisiStavke(){
        $this->stavke= [];
    }

    public function addStavke(StavkaFakture $stavke): self {
        if (!$this->stavke->contains($stavke)) {
            $this->stavke[] = $stavke;
            $stavke->setFaktura($this);
        }

        return $this;
    }

    public function removeStavke(StavkaFakture $stavke): self {
        if ($this->stavke->removeElement($stavke)) {
            // set the owning side to null (unless already changed)
            if ($stavke->getFaktura() === $this) {
                $stavke->setFaktura(null);
            }
        }

        return $this;
    }

    public function getOrganizacija(): ?Organizacija {
        return $this->organizacija;
    }

    public function setOrganizacija(?Organizacija $organizacija): self {
        $this->organizacija = $organizacija;

        return $this;
    }

    public function getUkupanIznos(){
        $iznos=0;
        foreach ($this->getStavke() as $stavka){
            $iznos+=$stavka->getKolicina()*$stavka->getProizvod()->getCenaPoJedinici();
        }
        return $iznos;
    }

}
