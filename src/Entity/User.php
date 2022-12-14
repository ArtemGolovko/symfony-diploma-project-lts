<?php

namespace App\Entity;

use App\Repository\UserRepository;
use App\Entity\ValueObject\Subscription;
use App\Service\Mailer\ReceiverInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface, ReceiverInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $verificationCode;

    /**
     * @ORM\Embedded(class=Subscription::class, columnPrefix="subscription_")
     */
    private $subscription;

    /**
     * @ORM\Column(type="string", length=180, nullable=true)
     */
    private $upgradeEmail;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $upgradeEmailVerificationCode;

    /**
     * @param $subscription
     */
    public function __construct()
    {
        $this->subscription = new Subscription();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }


    public function getVerificationCode(): ?string
    {
        return $this->verificationCode;
    }


    public function setVerificationCode(?string $verificationCode): self
    {
        $this->verificationCode = $verificationCode;

        return $this;
    }

    public function isVerified(): bool
    {
        return !$this->verificationCode;
    }

    /**
     * @return Subscription
     */
    public function getSubscription(): Subscription
    {
        return $this->subscription;
    }

    public function getUpgradeEmail(): ?string
    {
        return $this->upgradeEmail;
    }

    public function setUpgradeEmail(?string $upgradeEmail): self
    {
        $this->upgradeEmail = $upgradeEmail;

        return $this;
    }

    public function getUpgradeEmailVerificationCode(): ?string
    {
        return $this->upgradeEmailVerificationCode;
    }

    public function setUpgradeEmailVerificationCode(?string $upgradeEmailVerificationCode): self
    {
        $this->upgradeEmailVerificationCode = $upgradeEmailVerificationCode;

        return $this;
    }
}
