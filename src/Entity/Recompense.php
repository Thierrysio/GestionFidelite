<?php

namespace App\Entity;

use App\Repository\RecompenseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RecompenseRepository::class)]
class Recompense
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nomRecompense = null;

    #[ORM\ManyToOne(inversedBy: 'lesRecompenses')]
    private ?Palier $lePalier = null;

    #[ORM\Column]
    private ?int $pointsNecessaires = null;

    #[ORM\OneToMany(targetEntity: Utiliser::class, mappedBy: 'laRecompense')]
    private Collection $lesUtiliser;

    public function __construct()
    {
        $this->lesUtiliser = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomRecompense(): ?string
    {
        return $this->nomRecompense;
    }

    public function setNomRecompense(string $nomRecompense): static
    {
        $this->nomRecompense = $nomRecompense;

        return $this;
    }

    public function getLePalier(): ?Palier
    {
        return $this->lePalier;
    }

    public function setLePalier(?Palier $lePalier): static
    {
        $this->lePalier = $lePalier;

        return $this;
    }

    public function getPointsNecessaires(): ?int
    {
        return $this->pointsNecessaires;
    }

    public function setPointsNecessaires(int $pointsNecessaires): static
    {
        $this->pointsNecessaires = $pointsNecessaires;

        return $this;
    }

    /**
     * @return Collection<int, Utiliser>
     */
    public function getLesUtiliser(): Collection
    {
        return $this->lesUtiliser;
    }

    public function addLesUtiliser(Utiliser $lesUtiliser): static
    {
        if (!$this->lesUtiliser->contains($lesUtiliser)) {
            $this->lesUtiliser->add($lesUtiliser);
            $lesUtiliser->setLaRecompense($this);
        }

        return $this;
    }

    public function removeLesUtiliser(Utiliser $lesUtiliser): static
    {
        if ($this->lesUtiliser->removeElement($lesUtiliser)) {
            // set the owning side to null (unless already changed)
            if ($lesUtiliser->getLaRecompense() === $this) {
                $lesUtiliser->setLaRecompense(null);
            }
        }

        return $this;
    }
}
