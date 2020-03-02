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

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        $user = new User();

        $random = md5(random_bytes(60));
        $user->setEmail('admin@ylly.fr');
        $user->setName('Name');
        $user->setLastname('Lastname');
        $user->setPassword($this->encoder->encodePassword($user, 'demo'));
        $user->setRoles(['ROLE_ADMIN']);
        $user->setResetToken($random);
        $manager->persist($user);

        for ($i = 0; $i < 50; ++$i) {
            $setCreated = $faker->dateTimeBetween('-15 years', 'now');
            $user = new User();
            $user->setEmail($faker->email);
            $user->setName($faker->firstName);
            $user->setLastname($faker->lastName);
            $user->setPassword($this->encoder->encodePassword($user, 'cat'));
            $random = md5(random_bytes(60));
            $user->setResetToken($random);
            $user->setCreatedAt($setCreated);
            $user->setUpdatedAt($faker->dateTimeBetween($setCreated, 'now'));
            $manager->persist($user);
        }

        $manager->flush();
    }
}
