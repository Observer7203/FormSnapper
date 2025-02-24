<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

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

    #[ORM\OneToMany(targetEntity: Question::class, mappedBy: "form", cascade: ["persist", "remove"], orphanRemoval: true)]
    private Collection $questions;

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
        $this->questions = new ArrayCollection(); // Создание коллекции вопросов
    }

    public function getId(): ?int { return $this->id; }
    public function getTitle(): string { return $this->title; }
    public function setTitle(string $title): self { $this->title = $title; return $this; }
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): self { $this->description = $description; return $this; }
    
    public function getQuestions(): Collection { return $this->questions; }
    
    public function addQuestion(Question $question): self
    {
        if (!$this->questions->contains($question)) {
            $this->questions->add($question);
            $question->setForm($this);
        }
        return $this;
    }

    public function removeQuestion(Question $question): self
    {
        if ($this->questions->removeElement($question)) {
            if ($question->getForm() === $this) {
                $question->setForm(null);
            }
        }
        return $this;
    }

    public function getAuthor(): User { return $this->author; }
    public function setAuthor(User $author): self { $this->author = $author; return $this; }

    public function isPublic(): bool { return $this->isPublic; }
    public function setIsPublic(bool $isPublic): self { $this->isPublic = $isPublic; return $this; }

    public function getCreatedAt(): \DateTimeInterface { return $this->createdAt; }
}


