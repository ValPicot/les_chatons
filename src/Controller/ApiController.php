<?php

namespace App\Controller;

use App\Entity\Cat;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/api", name="api_")
 */
final class ApiController extends AbstractController
{
    /**
     * @Route("/", name="login")
     */
    public function login(Request $request)
    {
        return $this->json(['result' => true]);
        //return $this->render('security/api_login.html.twig');
    }

    /**
     * @Route("/cats", name="list_cats")
     * @IsGranted("ROLE_ADMIN")
     */
    public function listCats(SerializerInterface $serializer)
    {
        $cats = $this->getDoctrine()->getRepository(Cat::class)->findAll();

        $resultat = $serializer->serialize($cats, 'json', [
            'groups' => ['group1'],
        ]);

        return new JsonResponse($resultat, 200, [], true);
    }
}
