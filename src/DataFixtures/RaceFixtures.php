<?php

namespace App\DataFixtures;

use App\Entity\Race;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class RaceFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $race = new Race();
        $race->setName('Persan');
        $manager->persist($race);

        $race = new Race();
        $race->setName('Batard');
        $manager->persist($race);

        $race = new Race();
        $race->setName('Bengal');
        $manager->persist($race);

        $race = new Race();
        $race->setName('Main coon');
        $manager->persist($race);

        $race = new Race();
        $race->setName('Munchkin');
        $manager->persist($race);

        $race = new Race();
        $race->setName('Bleu russe');
        $manager->persist($race);

        $manager->flush();
    }
}
