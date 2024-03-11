<?php

namespace App\Entity;

use App\Repository\CommanderRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommanderRepository::class)]
class Commander
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $quantite = null;

    #[ORM\ManyToOne(inversedBy: 'lesCommander')]
    private ?User $leUser = null;

    #[ORM\ManyToOne(inversedBy: 'lesCommander')]
    private ?Commande $laCommande = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): static
    {
        $this->quantite = $quantite;

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

    public function getLaCommande(): ?Commande
    {
        return $this->laCommande;
    }

    public function setLaCommande(?Commande $laCommande): static
    {
        $this->laCommande = $laCommande;

        return $this;
    }
}
