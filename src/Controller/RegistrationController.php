<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Handler\UserCreateHandler;
use App\Form\Type\RegistrationType;
use App\Form\Type\UserType;
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
    public function registration(Request $request, UserCreateHandler $userCreateHandler)
    {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('cats_list');
        }

        $user = new User();
        $form = $this->createForm(UserType::class, $user, ['validation_groups' => ['Default', 'user_create']]);

        if ($userCreateHandler->process($form, $request)) {
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
