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
    /**
     * @param ObjectManager $manager
     *
     * @return void
     */
    protected function loadData(ObjectManager $manager): void
    {
        $this->createMany(Article::class, 30, function (Article $article) {
            /** @var User $author */
            $author = $this->getRandomReference(User::class);

            $article
                ->setTitle($this->faker->sentence)
                ->setContent($this->faker->paragraphs(5, true))
                ->setAuthor($author)
                ->setGenerateOptions($this->getGenerateOptions())
            ;
        });
    }

    /**
     * @return ArticleGenerateOptions
     */
    private function getGenerateOptions(): ArticleGenerateOptions
    {
        return (new ArticleGenerateOptions())
            ->setTheme($this->faker->word)
            ->setTitle($this->faker->boolean ? $this->faker->sentence : null)
            ->setKeywords($this->faker->words(6))
            ->setSize($this->getRange())
            ->setPromotedWords($this->getPromotedWords())
            ->setImages($this->getImages())
        ;
    }

    /**
     * @return Range
     */
    private function getRange(): Range
    {
        $begin = $this->faker->numberBetween(1, 3);
        $end = $this->faker->numberBetween($begin, 6);

        return Range::create($begin, $end);
    }

    /**
     * @return PromotedWord[]
     */
    private function getPromotedWords(): array
    {
        $promotedWords = [];
        $quantity = $this->faker->numberBetween(1, 5);

        for ($i = 0; $i < $quantity; $i++) {
            $promotedWords[] = PromotedWord::create($this->faker->word, $this->faker->numberBetween(1, 10));
        }

        return $promotedWords;
    }

    /**
     * @return string[]
     */
    private function getImages(): array
    {
        $images = [];
        $quantity = $this->faker->numberBetween(0, 5);

        for ($i = 0; $i < $quantity; $i++) {
            $images[] = $this->faker->imageUrl();
        }

        return $images;
    }

    /**
     * @return string[]
     */
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
