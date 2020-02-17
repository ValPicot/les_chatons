<?php
namespace App\Controller;

use App\Form\Handler\ForgotPasswordHandler;
use App\Form\Type\ForgotPasswordType;
use App\Form\Type\ResetPasswordType;
use App\Service\MailerService;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    private $em;

    private $mailer;

    public function __construct(ObjectManager $em, MailerService $mailer)
    {
        $this->em = $em;
        $this->mailer = $mailer;
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
    public function resetPassword(Request $request, string $token): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('cats_list');
        }

        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
//            $this->addFlash('success', 'flash.edit.user.success');
//
//            return $this->redirectToRoute('users_profile');
        }

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
            $this->addFlash('success', 'OK');
        }

        return $this->render('security/forgotpassword.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
