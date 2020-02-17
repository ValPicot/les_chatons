<?php

namespace App\Form\Handler;

use App\Form\Handler\Base\BaseHandler;
use App\Repository\UserRepository;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ForgotPasswordHandler extends BaseHandler
{
    private $userRepository;

    private $entityManager;

    private $mailer;

    private $translator;

    private $templating;

    public function __construct(UserRepository $userRepository, EntityManagerInterface $entityManager, MailerService $mailer, TranslatorInterface $translator, EngineInterface $templating)
    {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
        $this->translator = $translator;
        $this->templating = $templating;
    }

    public function onSuccess(): bool
    {
        $user = $this->userRepository->findOneBy(['email' => $this->form->getData()['email']]);
        if (null !== $user) {
            $random = md5(random_bytes(60));
            $user->setResetToken($random);
            $this->entityManager->flush();
            $bodyMail = $this->templating->render('emails/forgotPassword.html.twig', ['user' => $user]);
            $this->mailer->sendMail($bodyMail, 'noreply@leschatons.fr', $user->getEmail(), 'Réinitialiser votre mot de passe');

            return true;
        } else {
            //$this->request->getSession()->getFlashBag()->add('danger', 'error');
        }

        return false;
    }
}
