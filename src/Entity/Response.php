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

    #[ORM\ManyToOne(targetEntity: "App\Entity\Form")]
    #[ORM\JoinColumn(nullable: false)]
    private Form $form;

    #[ORM\ManyToOne(targetEntity: "App\Entity\User")]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\Column(type: "json")]
    private array $answers = []; // Ответы пользователя

    public function getId(): ?int { return $this->id; }
    public function getForm(): Form { return $this->form; }
    public function setForm(Form $form): self { $this->form = $form; return $this; }
    public function getUser(): User { return $this->user; }
    public function setUser(User $user): self { $this->user = $user; return $this; }
    public function getAnswers(): array { return $this->answers; }
    public function setAnswers(array $answers): self { $this->answers = $answers; return $this; }
}
