<?php

namespace App\Entity;

use App\Repository\PalierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PalierRepository::class)]
class Palier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $palierBas = null;

    #[ORM\Column]
    private ?int $palierHaut = null;

    #[ORM\OneToMany(targetEntity: Recompense::class, mappedBy: 'lePalier')]
    private Collection $lesRecompenses;

    #[ORM\Column(length: 255)]
    private ?string $nomPalier = null;

    public function __construct()
    {
        $this->lesRecompenses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPalierBas(): ?int
    {
        return $this->palierBas;
    }

    public function setPalierBas(int $palierBas): static
    {
        $this->palierBas = $palierBas;

        return $this;
    }

    public function getPalierHaut(): ?int
    {
        return $this->palierHaut;
    }

    public function setPalierHaut(int $palierHaut): static
    {
        $this->palierHaut = $palierHaut;

        return $this;
    }

    /**
     * @return Collection<int, Recompense>
     */
    public function getLesRecompenses(): Collection
    {
        return $this->lesRecompenses;
    }

    public function addLesRecompense(Recompense $lesRecompense): static
    {
        if (!$this->lesRecompenses->contains($lesRecompense)) {
            $this->lesRecompenses->add($lesRecompense);
            $lesRecompense->setLePalier($this);
        }

        return $this;
    }

    public function removeLesRecompense(Recompense $lesRecompense): static
    {
        if ($this->lesRecompenses->removeElement($lesRecompense)) {
            // set the owning side to null (unless already changed)
            if ($lesRecompense->getLePalier() === $this) {
                $lesRecompense->setLePalier(null);
            }
        }

        return $this;
    }

    public function getNomPalier(): ?string
    {
        return $this->nomPalier;
    }

    public function setNomPalier(string $nomPalier): static
    {
        $this->nomPalier = $nomPalier;

        return $this;
    }
}
