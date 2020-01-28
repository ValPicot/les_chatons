<?php


namespace App\Controller;

use App\Entity\User;
use App\Form\Type\RegistrationType;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{
    private $em;
    private $encoder;

    public function __construct(ObjectManager $em, UserPasswordEncoderInterface $encoder){
        $this->em = $em;
        $this->encoder = $encoder;
    }

    /**
     * @Route("/registration", name="registration")
     */
    public function registration(Request $request, \Swift_Mailer $mailer) {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')){
            return $this->redirectToRoute('cats_list');
        }

        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $user->setIsActive(0);
            $user->setCreatedAt(new \DateTime('now'));
            $user->setUpdatedAt(new \DateTime('now'));
            $user->setPassword($this->encoder->encodePassword($user, $user->getPassword()));

            $this->em->persist($user);
            $this->em->flush();

            $message = (new \Swift_Message('Hello Email'))
                ->setFrom('leschatons@ylly.fr')
                ->setTo($user->getEmail())
                ->setBody(
                    'test'
                )
            ;
            $mailer->send($message);
            //$this->addFlash('success', 'flash.create.cat.success');

            return $this->redirectToRoute('login');
        }
        return $this->render('users/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }
}