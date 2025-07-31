<?php
// src/Entity/User.php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name:"user")]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type:"integer")]
    private ?int $id = null;

    #[ORM\Column(type:"string", length:180, unique:true)]
    private ?string $username = null;

    #[ORM\Column(type:"string", length:180, unique:true)]
    private ?string $email = null;

    #[ORM\Column(type:"string")]
    private ?string $password = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Operation::class, cascade:["remove"])]
    private Collection $operations;

    public function __construct()
    {
        $this->operations = new ArrayCollection();
    }

    public function getId(): ?int 
    {
        return $this->id;
    }

    /**
     * Méthode obligatoire depuis Symfony 5.3, identifier unique du user
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * Garder getUsername si besoin legacy, mais non obligatoire
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;
        return $this;
    }

    public function getEmail(): string 
    {
        return (string) $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Méthode requise par PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return Collection|Operation[]
     */
    public function getOperations(): Collection 
    {
        return $this->operations;
    }

    /**
     * Retourne les rôles accordés à cet utilisateur
     */
    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    /**
     * Pas nécessaire avec bcrypt ou argon2i, retourner null
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * Nettoie les données sensibles temporaires (ex : champ plainPassword)
     * Nécessite la signature void, sans type de retour
     */
    public function eraseCredentials(): void
    {
        // Si tu stockais une donnée sensible temporaire, la vider ici
    }
}
