<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

abstract class BaseFixtures extends Fixture
{
    /**
     * @var Generator
     */
    protected Generator $faker;

    /**
     * @var ObjectManager
     */
    protected ObjectManager $manager;

    /**
     * @var array
     */
    private array $referencesIndex = [];

    /**
     * @param ObjectManager $manager
     *
     * @return void
     */
    abstract protected function loadData(ObjectManager $manager): void;

    /**
     * @param ObjectManager $manager
     *
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        $this->faker = Factory::create();
        $this->manager = $manager;

        $this->loadData($this->manager);

        $manager->flush();
    }

    /**
     * @param string   $className
     * @param callable $factory
     *
     * @return object
     */
    protected function create(string $className, callable $factory): object
    {
        $entity = new $className();
        $factory($entity);

        $this->manager->persist($entity);

        return $entity;
    }

    /**
     * @param string   $className
     * @param int      $quantity
     * @param callable $factory
     *
     * @return void
     */
    protected function createMany(string $className, int $quantity, callable $factory): void
    {
        for ($i = 0; $i < $quantity; $i++) {
            $entity = $this->create($className, $factory);

            $this->addReference($className . '|' . $i, $entity);
        }
    }

    /**
     * @param $className
     *
     * @return object
     * @throws \Exception
     */
    protected function getRandomReference($className): object
    {
        if (!isset($this->referencesIndex[$className])) {
            $this->referencesIndex[$className] = [];

            foreach ($this->referenceRepository->getReferences() as $key => $reference) {
                if (strpos($key, $className . '|') === 0) {
                    $this->referencesIndex[$className][] = $key;
                }
            }
        }

        if (empty($this->referencesIndex[$className])) {
            throw new \Exception('Не найдены ссылки на класс: ' . $className);
        }

        return $this->getReference($this->faker->randomElement($this->referencesIndex[$className]));
    }
}
