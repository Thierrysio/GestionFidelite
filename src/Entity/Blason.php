<?php

namespace App\Entity;

use App\Repository\BlasonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BlasonRepository::class)]
class Blason
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nomBlason = null;

    #[ORM\Column]
    private ?float $montantAchats = null;

    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'leBlason')]
    private Collection $lesUser;

    public function __construct()
    {
        $this->lesUser = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomBlason(): ?string
    {
        return $this->nomBlason;
    }

    public function setNomBlason(string $nomBlason): static
    {
        $this->nomBlason = $nomBlason;

        return $this;
    }

    public function getMontantAchats(): ?float
    {
        return $this->montantAchats;
    }

    public function setMontantAchats(float $montantAchats): static
    {
        $this->montantAchats = $montantAchats;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getLesUser(): Collection
    {
        return $this->lesUser;
    }

    public function addLesUser(User $lesUser): static
    {
        if (!$this->lesUser->contains($lesUser)) {
            $this->lesUser->add($lesUser);
            $lesUser->setLeBlason($this);
        }

        return $this;
    }

    public function removeLesUser(User $lesUser): static
    {
        if ($this->lesUser->removeElement($lesUser)) {
            // set the owning side to null (unless already changed)
            if ($lesUser->getLeBlason() === $this) {
                $lesUser->setLeBlason(null);
            }
        }

        return $this;
    }
}
