<?php

namespace App\Controller;

use App\Entity\Cat;
use App\Entity\User;
use App\Form\CatType;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/users", name="users_")
 */
class UsersController extends AbstractController
{
    private $userRepository;

    private $em;

    public function __construct(UserRepository $userRepository, ObjectManager $em){
        $this->userRepository = $userRepository;
        $this->em = $em;
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

    /**
     * @Route("/profile", name="profile")
     */
    public function profile() {
        return $this->render('users/profile.html.twig', [
            'user' => $this->getUser()
        ]);
    }

    /**
     * @Route("/create", name="create")
     * @IsGranted({"ROLE_ADMIN"})
     */
    public function create(Request $request) : Response {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($user);
            $this->em->flush();
            $this->addFlash('success', 'flash.create.cat.success');

            return $this->redirectToRoute('users_list');
        }
        return $this->render('users/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
