<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\OneToMany(targetEntity: Produit::class, mappedBy: 'leUser')]
    private Collection $lesProduits;

    #[ORM\OneToMany(targetEntity: Commande::class, mappedBy: 'leUser')]
    private Collection $lesCommandes;

    #[ORM\OneToMany(targetEntity: Commander::class, mappedBy: 'leUser')]
    private Collection $lesCommander;

    #[ORM\Column]
    private ?int $StockPointsFidelite = null;

    #[ORM\OneToMany(targetEntity: Utiliser::class, mappedBy: 'leUser')]
    private Collection $lesUtiliser;

    #[ORM\ManyToOne(inversedBy: 'lesUser')]
    private ?Blason $leBlason = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    public function __construct()
    {
        $this->lesProduits = new ArrayCollection();
        $this->lesCommandes = new ArrayCollection();
        $this->lesCommander = new ArrayCollection();
        $this->lesUtiliser = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, Produit>
     */
    public function getLesProduits(): Collection
    {
        return $this->lesProduits;
    }

    public function addLesProduit(Produit $lesProduit): static
    {
        if (!$this->lesProduits->contains($lesProduit)) {
            $this->lesProduits->add($lesProduit);
            $lesProduit->setLeUser($this);
        }

        return $this;
    }

    public function removeLesProduit(Produit $lesProduit): static
    {
        if ($this->lesProduits->removeElement($lesProduit)) {
            // set the owning side to null (unless already changed)
            if ($lesProduit->getLeUser() === $this) {
                $lesProduit->setLeUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Commande>
     */
    public function getLesCommandes(): Collection
    {
        return $this->lesCommandes;
    }

    public function addLesCommande(Commande $lesCommande): static
    {
        if (!$this->lesCommandes->contains($lesCommande)) {
            $this->lesCommandes->add($lesCommande);
            $lesCommande->setLeUser($this);
        }

        return $this;
    }

    public function removeLesCommande(Commande $lesCommande): static
    {
        if ($this->lesCommandes->removeElement($lesCommande)) {
            // set the owning side to null (unless already changed)
            if ($lesCommande->getLeUser() === $this) {
                $lesCommande->setLeUser(null);
            }
        }

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
            $lesCommander->setLeUser($this);
        }

        return $this;
    }

    public function removeLesCommander(Commander $lesCommander): static
    {
        if ($this->lesCommander->removeElement($lesCommander)) {
            // set the owning side to null (unless already changed)
            if ($lesCommander->getLeUser() === $this) {
                $lesCommander->setLeUser(null);
            }
        }

        return $this;
    }

    public function getStockPointsFidelite(): ?int
    {
        return $this->StockPointsFidelite;
    }

    public function setStockPointsFidelite(int $StockPointsFidelite): static
    {
        $this->StockPointsFidelite = $StockPointsFidelite;

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
            $lesUtiliser->setLeUser($this);
        }

        return $this;
    }

    public function removeLesUtiliser(Utiliser $lesUtiliser): static
    {
        if ($this->lesUtiliser->removeElement($lesUtiliser)) {
            // set the owning side to null (unless already changed)
            if ($lesUtiliser->getLeUser() === $this) {
                $lesUtiliser->setLeUser(null);
            }
        }

        return $this;
    }

    public function getLeBlason(): ?Blason
    {
        return $this->leBlason;
    }

    public function setLeBlason(?Blason $leBlason): static
    {
        $this->leBlason = $leBlason;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }
}
