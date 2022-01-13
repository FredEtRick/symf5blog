<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use DateTimeImmutable;
use App\Entity\Article;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Validator\Constraints\Length;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $users = [];

        for ($i=0; $i < 8; $i++) { 
            $user = new User();

            $firstname = "";

            if ($faker->numberBetween(0, 1) == 0)
                $firstname = $faker->firstNameFemale;
            else
                $firstname = $faker->firstNameMale;
            
            $password = "";
            
            for ($j=0; $j < 8; $j++) { 
                if ($faker->numberBetween(0, 2) == 0)
                    $password .= $faker->numberBetween(0, 9);
                else
                    $password .= $faker->randomLetter;
            }

            $user
                ->setUsername($faker->word)
                ->setFirstname($firstname)
                ->setLastname($faker->LastName)
                ->setEmail($faker->email)
                ->setPassword($password)
                ->setCreatedAt(new \DateTimeImmutable())
            ;

            $users[] = $user;
        }

        $categories = [];

        for ($i=0; $i < 15; $i++) { 
            $category = new Category();

            $image = $faker->numberBetween(1, 2) . ".png";

            $category
                ->setTitle($faker->sentence(3))
                ->setDescription($faker->text)
                ->setImage($image)
                ->setCreatedAt(new \DateTimeImmutable())
            ;

            $categories[] = $category;
        }

        $articles = [];

        for ($i=0; $i < 80; $i++) { 
            $article = new Article();

            $image = $faker->numberBetween(1, 2) . ".png";

            // author
            $nbAuthors = count($users);
            $indiceAuthor = $faker->numberBetween(0, $nbAuthors - 1);

            $article
                ->setTitle($faker->sentence(3))
                ->setContent($faker->text)
                ->setImage($image)
                ->setAuthor($users[$indiceAuthor])
                ->setCreatedAt(new \DateTimeImmutable())
            ;

            foreach ($categories as $category) {
                if ($faker->numberBetween(0,3) == 0)
                    $article->addCategory($category);
            }

            $articles[] = $article;
        }

        foreach ($users as $user) {
            $manager->persist($user);
        }
        foreach ($categories as $category) {
            $manager->persist($category);
        }
        foreach ($articles as $article) {
            $manager->persist($article);
        }

        $manager->flush();
    }
}
