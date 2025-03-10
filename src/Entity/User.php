<?php

namespace App\Entity;

use App\Enum\UserStatus;
use App\Repository\UserRepository;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;





#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: "users")]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{

    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['user_list'])]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank(message: "Email не должен быть пустым.")]
    #[Assert\Email(message: "Укажите корректный email.")]
    #[Groups(['user_list'])]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Пароль не должен быть пустым.")]
    #[Assert\Length(
        min: 6,
        minMessage: "Пароль должен содержать не менее {{ limit }} символов."
    )]
    private ?string $password = null;

    #[ORM\Column(type: Types::STRING, enumType: UserStatus::class)]
    #[Groups(['user_list'])]
    private UserStatus $status = UserStatus::Active;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['user_list'])]
    private ?\DateTimeInterface $lastLogin = null;

    #[ORM\Column(type: "json")]
    private array $roles = [];


    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $createdAt = null;

    #[Gedmo\Timestampable(on: 'update')]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTime $updatedAt = null;
    

    public function getId(): ?int { return $this->id; }
    public function getEmail(): ?string { return $this->email; }
    public function setEmail(string $email): self { $this->email = $email; return $this; }
    public function getPassword(): ?string { return $this->password; }
    public function setPassword(string $password): self { $this->password = $password; return $this; }
    public function getStatus(): UserStatus { return $this->status; }
    public function setStatus(UserStatus $status): self { $this->status = $status; return $this; }
    public function getLastLogin(): ?\DateTimeInterface { return $this->lastLogin; }
    public function setLastLogin(?\DateTimeInterface $lastLogin): self { $this->lastLogin = $lastLogin; return $this; }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER'; // Все пользователи - пользователи по умолчанию
        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

#[ORM\Column(type: Types::STRING, nullable: true)] // Не сохраняем в БД
private ?string $plainPassword = null;
public function getPlainPassword(): ?string
{
    return $this->plainPassword;
}

public function setPlainPassword(?string $plainPassword): self
{
    $this->plainPassword = $plainPassword;
    return $this;
}

    public function eraseCredentials(): void {}

    public function getUserIdentifier(): string { return $this->email; }




    public function getCreatedAt(): ?\DateTimeImmutable
{
    return $this->createdAt;
}
public function getUpdatedAt(): ?\DateTime
{
    return $this->updatedAt;
}

}
