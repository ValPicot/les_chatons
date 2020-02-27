<?php
namespace App\Controller;

use App\Form\Handler\ForgotPasswordHandler;
use App\Form\Handler\ResetPasswordHandler;
use App\Form\Type\ForgotPasswordType;
use App\Form\Type\ResetPasswordType;
use App\Repository\UserRepository;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    private $em;

    private $mailer;

    private $userRepository;

    private $encoder;

    public function __construct(EntityManagerInterface $em, MailerService $mailer, UserRepository $userRepository, UserPasswordEncoderInterface $encoder)
    {
        $this->em = $em;
        $this->mailer = $mailer;
        $this->userRepository = $userRepository;
        $this->encoder = $encoder;
    }
    /**
     * @Route("/login", name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('cats_list');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * @Route("/resetPassword/{token}", name="resetPassword")
     */
    public function resetPassword(Request $request, string $token, ResetPasswordHandler $handler): Response
    {
        $user = $this->userRepository->findOneBy(['resetToken' => $token]);
        $form = $this->createForm(ResetPasswordType::class, null);
        //$form->handleRequest($request);

        if ($handler->processPasswordChange($form, $request, $user)) {
            $this->addFlash('success', 'Test');
            //$this->addFlash('success', $translator->trans('password_change_success'));

            return $this->redirectToRoute('login');
        }

//        if ($form->isSubmitted() && $form->isValid()) {
//            $data = $form->getData();
//            dd($data);
//            $random = md5(random_bytes(60));
//            $user->setPassword($this->encoder->encodePassword($user, $data->getPassword()));
//            $user->setResetToken($random);
//            $this->em->flush();
//            $this->addFlash('success', 'flash.edit.user.success');
//
//            return $this->redirectToRoute('users_profile');
//        }

        return $this->render('security/resetpassword.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/forgotpassword", name="forgotPassword")
     */
    public function forgotPassword(Request $request, ForgotPasswordHandler $handler): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('cats_list');
        }

        $form = $this->createForm(ForgotPasswordType::class, null);

        if ($handler->process($form, $request)) {
            $this->addFlash('success', 'page.forgot_password.success');
        }

        return $this->render('security/forgotpassword.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
