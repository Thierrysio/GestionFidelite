<?php

namespace App\Entity;

use App\Repository\UtiliserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UtiliserRepository::class)]
class Utiliser
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateUtiliser = null;

    #[ORM\ManyToOne(inversedBy: 'lesUtiliser')]
    private ?Recompense $laRecompense = null;

    #[ORM\ManyToOne(inversedBy: 'lesUtiliser')]
    private ?User $leUser = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateUtiliser(): ?\DateTimeInterface
    {
        return $this->dateUtiliser;
    }

    public function setDateUtiliser(\DateTimeInterface $dateUtiliser): static
    {
        $this->dateUtiliser = $dateUtiliser;

        return $this;
    }

    public function getLaRecompense(): ?Recompense
    {
        return $this->laRecompense;
    }

    public function setLaRecompense(?Recompense $laRecompense): static
    {
        $this->laRecompense = $laRecompense;

        return $this;
    }

    public function getLeUser(): ?User
    {
        return $this->leUser;
    }

    public function setLeUser(?User $leUser): static
    {
        $this->leUser = $leUser;

        return $this;
    }
}
