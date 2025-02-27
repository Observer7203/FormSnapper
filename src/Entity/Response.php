<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Response
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Form::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Form $form;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\Column(type: "json")]
    private array $answers = [];

    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $score = null;

    #[ORM\Column(type: "json", nullable: true)]
    private ?array $scores = null;

    public function getId(): ?int { return $this->id; }
    public function getForm(): Form { return $this->form; }
    public function setForm(Form $form): self { $this->form = $form; return $this; }
    public function getUser(): User { return $this->user; }
    public function setUser(User $user): self { $this->user = $user; return $this; }
    public function getAnswers(): array { return $this->answers; }
    public function setAnswers(array $answers): self { $this->answers = $answers; return $this; }
    public function getScore(): ?int { return $this->score; }
    public function setScore(?int $score): self { $this->score = $score; return $this; }

    public function setScores(array $scores): self
    {
        $this->scores = $scores;
        return $this;
    }

    public function getScores(): array
    {
        return $this->scores ?? []; // Если null, возвращаем пустой массив
    }
    
}
