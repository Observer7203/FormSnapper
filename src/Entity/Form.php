<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Form
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank]
    private string $title;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: "json")]
    private array $questions = []; // Список вопросов

    #[ORM\ManyToOne(targetEntity: "App\Entity\User")]
    #[ORM\JoinColumn(nullable: false)]
    private User $author;

    #[ORM\Column(type: "boolean", options: ["default" => false])]
    private bool $isPublic = false; // Форма публичная или нет

    #[ORM\Column(type: "datetime", options: ["default" => "CURRENT_TIMESTAMP"])]
    private \DateTimeInterface $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }
    public function getTitle(): string { return $this->title; }
    public function setTitle(string $title): self { $this->title = $title; return $this; }
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): self { $this->description = $description; return $this; }
    public function getQuestions(): array { return $this->questions; }
    public function setQuestions(array $questions): self { $this->questions = $questions; return $this; }
    public function getAuthor(): User { return $this->author; }
    public function setAuthor(User $author): self { $this->author = $author; return $this; }

    public function isPublic(): bool { return $this->isPublic; }
    public function setIsPublic(bool $isPublic): self { $this->isPublic = $isPublic; return $this; }

    public function getCreatedAt(): \DateTimeInterface { return $this->createdAt; }
}

