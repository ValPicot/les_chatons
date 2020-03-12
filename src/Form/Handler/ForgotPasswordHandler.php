<?php

namespace App\Form\Handler;

use App\Form\Handler\Base\BaseHandler;
use App\Repository\UserRepository;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ForgotPasswordHandler extends BaseHandler
{
    private $userRepository;

    private $entityManager;

    private $mailer;

    private $translator;

    private $container;

    public function __construct(UserRepository $userRepository, EntityManagerInterface $entityManager, MailerService $mailer, TranslatorInterface $translator, ContainerInterface $container)
    {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
        $this->translator = $translator;
        $this->container = $container;
    }

    public function onSuccess(): bool
    {
        $user = $this->userRepository->findOneBy(['email' => $this->form->getData()['email']]);
        if (null !== $user) {
            $random = md5(random_bytes(60));
            $user->setResetToken($random);
            $this->entityManager->flush();
            $bodyMail = $this->container->get('twig')->render('emails/forgotPassword.html.twig', ['user' => $user]);
            $this->mailer->sendMail($bodyMail, 'noreply@leschatons.fr', $user->getEmail(), 'Réinitialiser votre mot de passe');

            return true;
        }

        return false;
    }
}
