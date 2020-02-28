<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\ProfileType;
use App\Form\Type\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/users", name="users_")
 */
class UsersController extends AbstractController
{
    private $userRepository;

    private $em;

    private $encoder;

    public function __construct(UserRepository $userRepository, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder)
    {
        $this->userRepository = $userRepository;
        $this->em = $em;
        $this->encoder = $encoder;
    }

    /**
     * @Route("/list", name="list")
     * @IsGranted({"ROLE_ADMIN"})
     */
    public function list()
    {
        $users = $this->userRepository->findAll();

        return $this->render('users/list.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/profile", name="profile")
     */
    public function profile()
    {
        return $this->render('users/profile.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    /**
     * @Route("/create", name="create")
     * @IsGranted({"ROLE_ADMIN"})
     */
    public function create(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $random = md5(random_bytes(60));
            $user->setCreatedAt(new \DateTime('now'));
            $user->setUpdatedAt(new \DateTime('now'));
            $user->setPassword($this->encoder->encodePassword($user, $data->getPassword()));
            $user->setResetToken($random);
            $this->em->persist($user);
            $this->em->flush();
            $this->addFlash('success', 'flash.create.cat.success');

            return $this->redirectToRoute('users_list');
        }

        return $this->render('users/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit", name="edit")
     */
    public function edit(Request $request)
    {
        $user = $this->getUser();

        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'flash.edit.user.success');

            return $this->redirectToRoute('users_profile');
        }

        return $this->render('users/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
