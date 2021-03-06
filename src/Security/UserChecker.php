<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\AccountExpiredException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function checkPreAuth(UserInterface $user)
    {
        if (!$user instanceof User) {
            return;
        }

        // user is deleted, show a generic Account Not Found message.
        if (!$user->getIsActive()) {
            throw new AccountExpiredException();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function checkPostAuth(UserInterface $user)
    {
        // TODO: Implement checkPostAuth() method.
    }
}
