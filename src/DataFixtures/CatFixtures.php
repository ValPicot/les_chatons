<?php

namespace App\DataFixtures;

use App\Entity\Cat;
use App\Entity\Race;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class CatFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        $users = $manager->getRepository(User::class)->findAll();
        $races = $manager->getRepository(Race::class)->findAll();
        for ($i = 0; $i < 200; ++$i) {
            $cat = new Cat();
            $cat
                ->setName($faker->firstName)
                ->setColor($faker->hexColor)
                ->setFilename($faker->imageUrl(100, 75, 'cats'))
                ->setUser($faker->randomElement($users))
                ->setRace($faker->randomElement($races))
            ;
            $manager->persist($cat);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
            RaceFixtures::class,
        ];
    }
}
