<?php

namespace App\DataFixtures;

use Faker\Factory;
use Faker\Generator;
use App\Entity\Articles;
use App\Entity\User;
use App\Entity\Comments;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;


class AppFixtures extends Fixture
{

    /**
     * @var Generator
     */
    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        for ($j = 0; $j < 50; $j++) {
            $user = new User();
            $user->setEmail($this->faker->email())
                ->setPassword($this->faker->password());
            $manager->persist($user);

            $comments = new Comments();
            $comments->setTitle($this->faker->sentence(1))
                ->setDescription($this->faker->paragraph())
                ->setUser($user);
            $manager->persist($comments);

            $article = new Articles();
            $article->setTitle($this->faker->sentence(1))
                ->setDescription($this->faker->paragraph())
                ->setImage($this->faker->imageUrl(640, 480, 'animals', true))
                ->setCreatedAt(\DateTimeImmutable::createFromMutable($this->faker->datetime()))                   
                ->setUser($user)
                ->addComment($comments);
            $manager->persist($article);
    
        }
        $manager->flush();
    }
}