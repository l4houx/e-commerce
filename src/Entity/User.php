<?php

namespace App\Entity;

use App\Entity\Traits\HasIsTeamTrait;
use Doctrine\DBAL\Types\Types;
use App\Entity\Traits\HasRoles;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use function Symfony\Component\String\u;
use App\Entity\Traits\HasRegistrationDetailsTrait;
use App\Entity\Traits\HasTimestampableTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_USERNAME', fields: ['username'])]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['username'], message: 'There is already an account with this username')]
class User implements UserInterface, PasswordAuthenticatedUserInterface, \Stringable
{
    use HasRegistrationDetailsTrait;
    use HasIsTeamTrait;
    use HasTimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\Length(min: 2, max: 20)]
    #[Assert\NotBlank]
    #[ORM\Column(type: Types::STRING, length: 20)]
    private string $firstname = '';

    #[Assert\Length(min: 2, max: 20)]
    #[Assert\NotBlank]
    #[ORM\Column(type: Types::STRING, length: 20)]
    private string $lastname = '';

    #[ORM\Column(type: Types::STRING, length: 30, unique: true)]
    #[Assert\NotBlank(message: "Please don't leave your username blank!")]
    #[Assert\Length(
        min: 4,
        max: 30,
        minMessage: 'The username is too short ({{ limit }} characters minimum)',
        maxMessage: 'The username is too long ({ limit } characters maximum)'
    )]
    private string $username = '';

    #[Assert\NotBlank]
    #[Assert\Email]
    #[Assert\NotNull]
    #[Assert\Length(min: 5, max: 180)]
    #[ORM\Column(type: Types::STRING, length: 180, unique: true)]
    private string $email = '';

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $avatar = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column(type: Types::JSON)]
    private array $roles = [HasRoles::DEFAULT];

    /**
     * @var string The hashed password
     */
    #[ORM\Column(type: Types::STRING)]
    private ?string $password = null;

    public function __construct()
    {
        $this->isVerified = false;
    }

    public function __toString(): string
    {
        return (string) $this->getFullName();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getFullName(): string
    {
        return u(sprintf('%s %s', $this->firstname, $this->lastname))->upper()->toString();
    }

    public function getFirstname(): ?string
    {
        return u($this->firstname)->upper()->toString();
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return u($this->lastname)->upper()->toString();
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(?string $username): static
    {
        $this->username = trim($username ?: '');

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email ?: '';

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(string $avatar): static
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole(HasRoles::SUPERADMIN);
    }

    public function setSuperAdmin($boolean): static
    {
        if (true === $boolean) {
            $this->addRole(HasRoles::SUPERADMIN);
        } else {
            $this->removeRole(HasRoles::SUPERADMIN);
        }

        return $this;
    }

    public function removeRole(string $role): static
    {
        if (false !== $key = array_search(strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }

        return $this;
    }

    public function hasRole(string $role): bool
    {
        return in_array(strtoupper($role), $this->getRoles(), true);
    }

    public function getRole(): string
    {
        if ($this->hasRole(HasRoles::SUPERADMIN) || $this->hasRole(HasRoles::ADMINAPPLICATION)) {
            return '<span class="badge me-2 bg-danger">Administrator</span>';
        } elseif ($this->hasRole(HasRoles::ADMIN)) {
            return '<span class="badge me-2 bg-dark">Admin</span>';
        } elseif ($this->hasRole(HasRoles::MODERATOR)) {
            return '<span class="badge me-2 bg-warning">Moderator</span>';
        } elseif ($this->hasRole(HasRoles::TEAM)) {
            return '<span class="badge me-2 bg-primary">Team</span>';
        } elseif ($this->hasRole(HasRoles::EDITOR)) {
            return '<span class="badge me-2 bg-secondary">Editor</span>';
        } elseif ($this->hasRole(HasRoles::DEFAULT)) {
            return '<span class="badge me-2 bg-success">User</span>';
        } else {
            return '<span class="badge me-2 bg-warning">N/A</span>';
        }
    }

    public function getCrossRoleName(): string
    {
        if ($this->hasRole(HasRoles::ADMIN)) {
            return $this->getFullName();
        } elseif ($this->hasRole(HasRoles::MODERATOR)) {
            return $this->getFullName();
        } elseif ($this->hasRole(HasRoles::TEAM)) {
            return $this->getFullName();
        } elseif ($this->hasRole(HasRoles::EDITOR)) {
            return $this->getFullName();
        } elseif ($this->hasRole(HasRoles::DEFAULT)) {
            return $this->getFullName();
        } else {
            return 'N/A';
        }
    }

    public function addRole(string $role): static
    {
        $role = strtoupper($role);
        if (HasRoles::DEFAULT === $role) {
            return $this;
        }

        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
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
        $roles[] = HasRoles::DEFAULT;

        if ($this->isVerified) {
            $roles[] = HasRoles::VERIFIED;
        }

        return array_values(array_unique($roles));
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        // $this->roles = $roles;

        $this->roles = [];

        foreach ($roles as $role) {
            $this->addRole($role);
        }

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

    public function __serialize(): array
    {
        return [
            $this->id,
            $this->username,
            $this->firstname,
            $this->lastname,
            $this->email,
            $this->password,
        ];
    }

    public function __unserialize(array $data): void
    {
        if (count($data) === 6) {
            [
                $this->id,
                $this->username,
                $this->firstname,
                $this->lastname,
                $this->email,
                $this->password,
            ] = $data;
        }
    }
}
