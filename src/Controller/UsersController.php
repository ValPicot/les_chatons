<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/users", name="users_")
 */
class UsersController extends AbstractController
{
    private $userRepository;

    public function __construct(UserRepository $userRepository){
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/list", name="list")
     * @IsGranted({"ROLE_ADMIN"})
     */
    public function list() {
        $users = $this->userRepository->findAll();

        return $this->render('users/list.html.twig', [
            'users' => $users
        ]);
    }
}
