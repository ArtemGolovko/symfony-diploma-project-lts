<?php

namespace App\Entity;

use App\Entity\ValueObject\ArticleGenerateOptions;
use App\Repository\ArticleRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ArticleRepository::class)
 * @ORM\Table(name="articles")
 * @ORM\HasLifecycleCallbacks()
 */
class Article
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @var int
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(max=255)
     * @var string
     */
    private string $title;

    /**
     * @ORM\Column(type="text")
     * @var string
     */
    private string $content;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="articles")
     * @ORM\JoinColumn(nullable=false)
     * @var User
     */
    private User $author;

    /**
     * @ORM\Embedded(class=ArticleGenerateOptions::class, columnPrefix="generate_options_")
     * @Assert\Valid
     * @var ArticleGenerateOptions
     */
    private ArticleGenerateOptions $generateOptions;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @var \DateTimeImmutable
     */
    private \DateTimeImmutable $createdAt;

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
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return Article
     */
    public function setTitle(string $title): Article
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     *
     * @return Article
     */
    public function setContent(string $content): Article
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return User
     */
    public function getAuthor(): User
    {
        return $this->author;
    }

    /**
     * @param User $author
     *
     * @return Article
     */
    public function setAuthor(User $author): Article
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return ArticleGenerateOptions
     */
    public function getGenerateOptions(): ArticleGenerateOptions
    {
        return $this->generateOptions;
    }

    /**
     * @param ArticleGenerateOptions $generateOptions
     *
     * @return Article
     */
    public function setGenerateOptions(ArticleGenerateOptions $generateOptions): Article
    {
        $this->generateOptions = $generateOptions;

        return $this;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreatedAt(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }
}
