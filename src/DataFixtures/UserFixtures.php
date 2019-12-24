<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder){
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        $user = new User();
        //$user->setUsername('demo');
        $user->setEmail('admin@ylly.fr');
        $user->setName('Name');
        $user->setLastname('Lastname');
        $user->setPassword($this->encoder->encodePassword($user, 'demo'));
        $user->setRoles(['ROLE_ADMIN']);
        $user->setCreatedAt(new \DateTime('now'));
        $user->setUpdatedAt(new \DateTime('now'));
        $manager->persist($user);

//        $user = new User();
//        $user->setUsername('cat');
//        $user->setPassword($this->encoder->encodePassword($user, 'cat'));
//        $user->setCreatedAt(new \DateTime('now'));
//        $user->setUpdatedAt(new \DateTime('now'));
//        $manager->persist($user);

        for ($i = 0; $i < 50; $i++) {
            $setCreated = $faker->dateTimeBetween('-15 years', 'now');
            $user = new User();
            //$user->setUsername($faker->name);
            $user->setEmail($faker->email);
            $user->setName($faker->name);
            $user->setLastname($faker->lastName);
            $user->setPassword($this->encoder->encodePassword($user, 'cat'));
            $user->setCreatedAt($setCreated);
            $user->setUpdatedAt($faker->dateTimeBetween($setCreated, 'now'));
            $manager->persist($user);
        }

        $manager->flush();
    }
}
