<?php

namespace App\Entity;

use App\Repository\ProizvodRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProizvodRepository::class)]
class Proizvod
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $nazivProizvoda;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private $cenaPoJedinici;

    #[ORM\ManyToOne(targetEntity: JedinicaMere::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $jedinicaMere;

    #[ORM\OneToMany(mappedBy: 'proizvod', targetEntity: StavkaFakture::class)]
    private $stavkaFaktures;

    public function __construct()
    {
        $this->stavkaFaktures = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNazivProizvoda(): ?string
    {
        return $this->nazivProizvoda;
    }

    public function setNazivProizvoda(string $nazivProizvoda): self
    {
        $this->nazivProizvoda = $nazivProizvoda;

        return $this;
    }

    public function getCenaPoJedinici(): ?string
    {
        return $this->cenaPoJedinici;
    }

    public function setCenaPoJedinici(string $cenaPoJedinici): self
    {
        $this->cenaPoJedinici = $cenaPoJedinici;

        return $this;
    }

    public function getJedinicaMere(): ?JedinicaMere
    {
        return $this->jedinicaMere;
    }

    public function setJedinicaMere(?JedinicaMere $jedinicaMere): self
    {
        $this->jedinicaMere = $jedinicaMere;

        return $this;
    }

    /**
     * @return Collection<int, StavkaFakture>
     */
    public function getStavkaFaktures(): Collection
    {
        return $this->stavkaFaktures;
    }

    public function addStavkaFakture(StavkaFakture $stavkaFakture): self
    {
        if (!$this->stavkaFaktures->contains($stavkaFakture)) {
            $this->stavkaFaktures[] = $stavkaFakture;
            $stavkaFakture->setProizvod($this);
        }

        return $this;
    }

    public function removeStavkaFakture(StavkaFakture $stavkaFakture): self
    {
        if ($this->stavkaFaktures->removeElement($stavkaFakture)) {
            // set the owning side to null (unless already changed)
            if ($stavkaFakture->getProizvod() === $this) {
                $stavkaFakture->setProizvod(null);
            }
        }

        return $this;
    }
}
