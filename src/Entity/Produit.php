<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nomProduit = null;

    #[ORM\Column]
    private ?float $prixProduit = null;

    #[ORM\Column]
    private ?int $pointsFidelite = null;

    #[ORM\ManyToOne(inversedBy: 'lesProduits')]
    private ?User $leUser = null;

    #[ORM\ManyToOne(inversedBy: 'lesProduits')]
    private ?Categorie $laCategorie = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageUrl = null;

    #[ORM\OneToMany(targetEntity: Commander::class, mappedBy: 'leProduit')]
    private Collection $lesCommander;

    public function __construct()
    {
        $this->lesCommander = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomProduit(): ?string
    {
        return $this->nomProduit;
    }

    public function setNomProduit(string $nomProduit): static
    {
        $this->nomProduit = $nomProduit;

        return $this;
    }

    public function getPrixProduit(): ?float
    {
        return $this->prixProduit;
    }

    public function setPrixProduit(float $prixProduit): static
    {
        $this->prixProduit = $prixProduit;

        return $this;
    }

    public function getPointsFidelite(): ?int
    {
        return $this->pointsFidelite;
    }

    public function setPointsFidelite(int $pointsFidelite): static
    {
        $this->pointsFidelite = $pointsFidelite;

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

    public function getLaCategorie(): ?Categorie
    {
        return $this->laCategorie;
    }

    public function setLaCategorie(?Categorie $laCategorie): static
    {
        $this->laCategorie = $laCategorie;

        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(?string $imageUrl): static
    {
        $this->imageUrl = $imageUrl;

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
            $lesCommander->setLeProduit($this);
        }

        return $this;
    }

    public function removeLesCommander(Commander $lesCommander): static
    {
        if ($this->lesCommander->removeElement($lesCommander)) {
            // set the owning side to null (unless already changed)
            if ($lesCommander->getLeProduit() === $this) {
                $lesCommander->setLeProduit(null);
            }
        }

        return $this;
    }
}
