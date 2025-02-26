<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Question
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Form::class, inversedBy: "questions")]
    #[ORM\JoinColumn(nullable: false)]
    private Form $form;

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank]
    private string $text;

    #[ORM\Column(type: "string", length: 50)]
    #[Assert\Choice(choices: ["text", "multiple_choice", "checkbox", "number", "radio", "file_upload", "rating", "scale"])]
    private string $type;

    #[ORM\Column(type: "json", nullable: true)]
    private ?array $options = null; // Для radio, checkbox, scale

    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $maxScale = null; // Для шкалы (scale)

    public function getId(): ?int { return $this->id; }
    public function getText(): string { return $this->text; }
    public function setText(string $text): self { $this->text = $text; return $this; }
    public function getType(): string { return $this->type; }
    public function setType(string $type): self { $this->type = $type; return $this; }
    public function getOptions(): ?array { return $this->options; }
    public function setOptions(?array $options): self { $this->options = $options; return $this; }
    public function getMaxScale(): ?int { return $this->maxScale; }
    public function setMaxScale(?int $maxScale): self { $this->maxScale = $maxScale; return $this; }

    public function getForm(): Form
{
    return $this->form;
}

public function setForm(?Form $form): self
{
    $this->form = $form;
    return $this;
}

}
