<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Dto\PromotedWord;
use App\Entity\User;
use App\Entity\ValueObject\ArticleGenerateOptions;
use App\Entity\ValueObject\Range;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ArticleFixtures extends BaseFixtures implements DependentFixtureInterface
{
    function loadData(ObjectManager $manager)
    {
        $this->createMany(Article::class, 30, function (Article $article) {
            /** @var User $author */
            $author = $this->getRandomReference(User::class);

            $article
                ->setTitle($this->faker->sentence)
                ->setDescription($this->faker->text(255))
                ->setContent($this->faker->paragraphs(5, true))
                ->setAuthor($author)
                ->setGenerateOptions($this->getGenerateOptions())
            ;
        });
    }

    /**
     * @return ArticleGenerateOptions
     */
    function getGenerateOptions(): ArticleGenerateOptions
    {
        return new ArticleGenerateOptions(
            $this->faker->word,
            $this->faker->words(6),
            $this->getRange(),
            $this->getPromotedWords(),
            $this->faker->boolean ? $this->faker->sentence : null,
            $this->getImages()
        );
    }

    /**
     * @return Range
     */
    function getRange(): Range
    {
        $begin = $this->faker->numberBetween(1, 3);
        $end = $this->faker->numberBetween($begin, 6);

        return new Range($begin, $end);
    }

    /**
     * @return array
     */
    function getPromotedWords(): array
    {
        $promotedWords = [];
        $quantity = $this->faker->numberBetween(1, 5);

        for ($i = 0; $i < $quantity; $i++) {
            $promotedWords[] = new PromotedWord($this->faker->word, $this->faker->numberBetween(1, 10));
        }

        return $promotedWords;
    }

    function getImages(): array
    {
        $images = [];
        $quantity = $this->faker->numberBetween(0, 5);

        for ($i = 0; $i < $quantity; $i++) {
            $images[] = $this->faker->imageUrl();
        }

        return $images;
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}