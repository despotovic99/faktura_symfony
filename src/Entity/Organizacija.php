<?php

namespace App\Entity;

use App\Repository\OrganizacijaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrganizacijaRepository::class)]
class Organizacija
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $naziv;

    #[ORM\OneToMany(mappedBy: 'organizacija', targetEntity: Faktura::class)]
    private $fakture;

    public function __construct()
    {
        $this->fakture = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, Faktura>
     */
    public function getFakture(): Collection
    {
        return $this->fakture;
    }

    public function addFakture(Faktura $fakture): self
    {
        if (!$this->fakture->contains($fakture)) {
            $this->fakture[] = $fakture;
            $fakture->setOrganizacija($this);
        }

        return $this;
    }

    public function removeFakture(Faktura $fakture): self
    {
        if ($this->fakture->removeElement($fakture)) {
            // set the owning side to null (unless already changed)
            if ($fakture->getOrganizacija() === $this) {
                $fakture->setOrganizacija(null);
            }
        }

        return $this;
    }

    public function __toString(): string {
      return $this->getNaziv();
    }

}
