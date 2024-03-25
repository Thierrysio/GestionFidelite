<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateCommande = null;

    #[ORM\ManyToOne(inversedBy: 'lesCommandes')]
    private ?User $leUser = null;

    #[ORM\OneToMany(targetEntity: Commander::class, mappedBy: 'laCommande')]
    private Collection $lesCommander;

    #[ORM\Column(length: 255, nullable: true)]
private ?string $etat = null;

    public function __construct()
    {
        $this->lesCommander = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateCommande(): ?\DateTimeInterface
    {
        return $this->dateCommande;
    }

    public function setDateCommande(\DateTimeInterface $dateCommande): static
    {
        $this->dateCommande = $dateCommande;

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

    /**
     * @return Collection<int, Commander>
     */
    public function getLesCommander(): Collection
    {
        return $this->lesCommander;
    }

    public function addLesCommander(Commander $lesCommander): static
    {
        if (!$this->lesCommander->contains($lesCommander)) {
            $this->lesCommander->add($lesCommander);
            $lesCommander->setLaCommande($this);
        }

        return $this;
    }

    public function removeLesCommander(Commander $lesCommander): static
    {
        if ($this->lesCommander->removeElement($lesCommander)) {
            // set the owning side to null (unless already changed)
            if ($lesCommander->getLaCommande() === $this) {
                $lesCommander->setLaCommande(null);
            }
        }

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): static
    {
        $this->etat = $etat;

        return $this;
    }
}
