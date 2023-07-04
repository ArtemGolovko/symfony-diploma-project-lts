<?php

namespace App\Entity;

use App\Entity\ValueObject\Subscription;
use App\Repository\UserRepository;
use App\Service\Mailer\ReceiverInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="users")
 */
class User implements UserInterface, ReceiverInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @var int
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @var string
     */
    private string $email;

    /**
     * @ORM\Column(type="json")
     * @var string[]
     */
    private array $roles = [];

    /**
     * @ORM\Column(type="string")
     * @var string The hashed password
     */
    private string $password;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private string $name;

    /**
     * @ORM\Column(type="boolean", length=255)
     * @var bool
     */
    private bool $isVerified = false;

    /**
     * @ORM\Embedded(class=Subscription::class, columnPrefix="subscription_")
     * @var Subscription
     */
    private Subscription $subscription;

    /**
     * @ORM\OneToMany(targetEntity=Article::class, mappedBy="author", orphanRemoval=true)
     * @var Collection<int, Article>
     */
    private Collection $articles;

    /**
     * @ORM\OneToMany(targetEntity=Module::class, mappedBy="author", orphanRemoval=true)
     * @var Collection<int, Module>
     */
    private Collection $modules;

    public function __construct()
    {
        $this->subscription = new Subscription();
        $this->articles = new ArrayCollection();
        $this->modules = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return User
     */
    public function setEmail(string $email): User
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     *
     * @return string
     */
    public function getUsername(): string
    {
        return (string)$this->email;
    }

    /**
     * @see UserInterface
     * @return string[]
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param string[] $roles
     *
     * @return $this
     */
    public function setRoles(array $roles): User
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     *
     * @return User
     */
    public function setPassword(string $password): User
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     * @return string|null
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     * @return void
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return bool
     */
    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    /**
     * @param bool $isVerified
     *
     * @return User
     */
    public function setIsVerified(bool $isVerified): User
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * @return Subscription
     */
    public function getSubscription(): Subscription
    {
        return $this->subscription;
    }

    /**
     * @return Collection<int, Article>
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    /**
     * @param Article $article
     *
     * @return User
     */
    public function addArticle(Article $article): User
    {
        if (!$this->articles->contains($article)) {
            $this->articles[] = $article;
            $article->setAuthor($this);
        }

        return $this;
    }

    /**
     * @param Article $article
     *
     * @return user
     */
    public function removeArticle(Article $article): User
    {
        if ($this->articles->contains($article)) {
            $this->articles->removeElement($article);
        }

        return $this;
    }

    /**
     * @return Collection<int, Module>
     */
    public function getModules(): Collection
    {
        return $this->modules;
    }

    /**
     * @param Module $module
     *
     * @return User
     */
    public function addModule(Module $module): User
    {
        if (!$this->modules->contains($module)) {
            $this->modules[] = $module;
            $module->setAuthor($this);
        }

        return $this;
    }

    /**
     * @param Module $module
     *
     * @return User
     */
    public function removeModule(Module $module): User
    {
        if ($this->modules->contains($module)) {
            $this->removeModule($module);
        }

        return $this;
    }
}
