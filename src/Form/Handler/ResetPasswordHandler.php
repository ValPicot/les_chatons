<?php

namespace App\Form\Handler;

use App\Entity\User;
use App\Form\Handler\Base\BaseHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
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
        return true;
    }

    public function processPasswordChange(FormInterface $form, Request $request, User $user): bool
    {
        if (parent::process($form, $request)) {
            $user->setPassword($this->userPasswordEncoder->encodePassword($user, $form->getData()['newPassword']));
            $this->entityManager->flush();

            //$user->setResetToken(null);
            $this->entityManager->flush();

            return $this->onSuccess();
        }

        return false;
    }
}
