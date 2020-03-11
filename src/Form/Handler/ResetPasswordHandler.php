<?php

namespace App\Form\Handler;

use App\Form\Handler\Base\BaseHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ResetPasswordHandler extends BaseHandler
{
    private $userPasswordEncoder;

    private $entityManager;

    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder, EntityManagerInterface $entityManager)
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->entityManager = $entityManager;
    }

    public function onSuccess(): bool
    {
        $user = $this->form->getConfig()->getOption('user');
        $user->setPassword($this->userPasswordEncoder->encodePassword($user, $this->form->getData()['newPassword']));

        $this->entityManager->flush();

        return true;
    }
}
