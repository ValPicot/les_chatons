<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\RegistrationType;
use App\Repository\UserRepository;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{
    private $em;
    private $encoder;
    private $userRepository;
    private $mailerService;

    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $encoder, UserRepository $userRepository, MailerService $mailerService)
    {
        $this->em = $em;
        $this->encoder = $encoder;
        $this->userRepository = $userRepository;
        $this->mailerService = $mailerService;
    }

    /**
     * @Route("/registration", name="registration")
     */
    public function registration(Request $request)
    {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('cats_list');
        }

        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $random = md5(random_bytes(60));
            $user->setIsActive(0);
            $user->setCreatedAt(new \DateTime('now'));
            $user->setUpdatedAt(new \DateTime('now'));
            $user->setPassword($this->encoder->encodePassword($user, $user->getPassword()));
            $user->setResetToken($random);
            $this->em->persist($user);
            $this->em->flush();

            $bodyMail = $this->renderView('emails/activeAccount.html.twig', ['user' => $user]);
            $this->mailerService->sendMail($bodyMail, 'noreply@leschatons.fr', $user->getEmail(), 'Confirmation d\'email');

            return $this->redirectToRoute('login');
        }

        return $this->render('users/registration.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/activated-account/{token}", name="activateAccount")
     */
    public function activateAccount(string $token)
    {
        $user = $this->userRepository->findOneBy(['resetToken' => $token]);
        if ($user) {
            $message = 'Votre compte à déjà été validé';
            if (!$user->getIsActive()) {
                $entityManager = $this->getDoctrine()->getManager();
                $user->setIsActive(1);
                $entityManager->flush();
                $message = 'Félicitation ! Votre compte est bien validé';
            }

            $this->container->get('session')->set('message', $message);

            return $this->redirectToRoute('confirm_email');
        } else {
            return $this->redirectToRoute('registration');
        }
    }

    /**
     * @Route("/confirm", name="confirm_email")
     */
    public function confirmEmail()
    {
        $message = $this->container->get('session')->get('message');

        return $this->render('users/validated.html.twig', [
            'message' => $message,
        ]);
    }
}
