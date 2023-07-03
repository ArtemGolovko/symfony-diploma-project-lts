<?php

namespace App\Entity\ValueObject;

use App\Entity\Dto\PromotedWord;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Embeddable
 */
class ArticleGenerateOptions
{
    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(max=255)
     */
    private string $theme;

    /**
     * @ORM\Column(type="simple_array")
     * @Assert\Count(min=1, max=6)
     * @var string[]
     */
    private array $keywords;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max=255)
     */
    private ?string $title;

    /**
     * @ORM\Embedded(class=Range::class, columnPrefix="size_")
     * @Assert\Valid
     */
    private Range $size;

    /**
     * @ORM\Column(type="array")
     * @var PromotedWord[]
     */
    private array $promotedWords;

    /**
     * @ORM\Column(type="simple_array", nullable=true)
     * @Assert\Count(max=5)
     * @var string[]
     */
    private array $images;

    /**
     * @param string         $theme
     * @param string[]       $keywords
     * @param string|null    $title
     * @param Range          $size
     * @param PromotedWord[] $promotedWords
     * @param string[]       $images
     */
    public function __construct(
        string $theme,
        array $keywords,
        Range $size,
        array $promotedWords,
        ?string $title = null,
        array $images = []
    ) {
        $this->theme = $theme;
        $this->keywords = $keywords;
        $this->title = $title;
        $this->size = $size;
        $this->promotedWords = $promotedWords;
        $this->images = $images;
    }

    /**
     * @return string
     */
    public function getTheme(): string
    {
        return $this->theme;
    }

    /**
     * @param string $theme
     *
     * @return ArticleGenerateOptions
     */
    public function setTheme(string $theme): self
    {
        $this->theme = $theme;

        return $this;
    }

    /**
     * @return array
     */
    public function getKeywords(): array
    {
        return $this->keywords;
    }

    /**
     * @param array $keywords
     *
     * @return ArticleGenerateOptions
     */
    public function setKeywords(array $keywords): self
    {
        $this->keywords = $keywords;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string|null $title
     *
     * @return ArticleGenerateOptions
     */
    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return Range
     */
    public function getSize(): Range
    {
        return $this->size;
    }

    /**
     * @return array
     */
    public function getPromotedWords(): array
    {
        return $this->promotedWords;
    }

    /**
     * @param array $promotedWords
     *
     * @return ArticleGenerateOptions
     */
    public function setPromotedWords(array $promotedWords): self
    {
        $this->promotedWords = $promotedWords;

        return $this;
    }

    /**
     * @return array
     */
    public function getImages(): array
    {
        return $this->images;
    }

    /**
     * @param array $images
     *
     * @return ArticleGenerateOptions
     */
    public function setImages(array $images): self
    {
        $this->images = $images;

        return $this;
    }
}