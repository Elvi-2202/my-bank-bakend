<?php
// src/Entity/Operation.php

namespace App\Entity;

use App\Repository\OperationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OperationRepository::class)]
class Operation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type:"integer")]
    private ?int $id = null;

    #[ORM\Column(type:"string", length:255)]
    private ?string $label = null;

    #[ORM\Column(type:"float")]
    private ?float $amount = null;

    #[ORM\Column(type:"datetime")]
    private ?\DateTimeInterface $date = null;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'operations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'operations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function getId(): ?int { return $this->id; }

    public function getLabel(): ?string { return $this->label; }
    public function setLabel(string $label): self
    {
        $this->label = $label;
        return $this;
    }

    public function getAmount(): ?float { return $this->amount; }
    public function setAmount(float $amount): self
    {
        $this->amount = $amount;
        return $this;
    }

    public function getDate(): ?\DateTimeInterface { return $this->date; }
    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;
        return $this;
    }

    public function getCategory(): ?Category { return $this->category; }
    public function setCategory(?Category $category): self
    {
        $this->category = $category;
        return $this;
    }

    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }
}
