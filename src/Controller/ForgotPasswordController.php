<?php

namespace App\Controller;

use App\Form\Type\ForgotPasswordType;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ForgotPasswordController extends AbstractController
{
    private $em;

    public function __construct(ObjectManager $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/forgotpassword", name="forgotPassword")
     */
    public function forgotPassword(Request $request): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('cats_list');
        }

        $form = $this->createForm(ForgotPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'flash.edit.user.success');

            return $this->redirectToRoute('users_profile');
        }

        return $this->render('users/forgotpassword.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
