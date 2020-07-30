<?php

namespace App\Form\Handler;

use App\Entity\User;
use App\Form\Handler\Base\BaseHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserCreateHandler extends BaseHandler
{
    private $userPasswordEncoder;
    private $entityManager;
    private $session;

    public function __construct(
        UserPasswordEncoderInterface $userPasswordEncoder,
        EntityManagerInterface $entityManager,
        SessionInterface $session
    ) {
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->entityManager = $entityManager;
        $this->session = $session;
    }

    public function onSuccess(): bool
    {
        /** @var User $user */
        $user = $this->form->getData();

        $random = md5(random_bytes(60));
        $user->setIsActive(0);
        $user->setPassword($this->userPasswordEncoder->encodePassword($user, $user->getPlainPassword()));
        $user->setResetToken($random);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return true;
    }
}
