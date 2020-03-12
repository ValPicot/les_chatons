<?php

namespace App\Service;

use App\Entity\User;

class RandomService
{
    private function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; ++$i) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    public function randomUser(User $user)
    {
        $randomString = $this->generateRandomString();

        $user->setName($randomString);
        $user->setLastname($randomString);
        $user->setEmail($randomString.'@leschatons.fr');
        $user->setIsActive(false);
        $user->setResetToken(null);
    }
}
