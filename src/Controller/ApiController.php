<?php

namespace App\Controller;

use App\Entity\Cat;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
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
    private $serializer;

    private $em;

    public function __construct(SerializerInterface $serializer, EntityManagerInterface $em)
    {
        $this->serializer = $serializer;
        $this->em = $em;
    }

    /**
     * @Route("/cats", name="list_cats", methods={"GET"})
     */
    public function listCats(PaginatorInterface $paginator, Request $request): JsonResponse
    {
        //Ajouter image dans api

        $cats = $paginator->paginate(
            $this->getDoctrine()->getRepository(Cat::class)->findVisibleCats($this->getUser()),
            $request->query->getInt('page', 1),
            15
        );

        $resultat = $this->serializer->serialize($cats, 'json', [
            'groups' => ['list_cats'],
        ]);

        return new JsonResponse($resultat, 200, [], true);
    }

    /**
     * @Route("/cat/{id}", name="cat_get", methods={"GET"})
     */
    public function getCat(Cat $cat): JsonResponse
    {
        if ($cat->getUser()->getId() === $this->getUser()->getId() || $this->getUser()->hasRoles(User::ROLE_ADMIN)) {
            $resultat = $this->serializer->serialize($cat, 'json', [
                'groups' => ['get_cat'],
            ]);

            return new JsonResponse($resultat, 200, [], true);
        } else {
            $data = [
                'message' => 'Accés refusé',
            ];
            $resultat = $this->serializer->serialize($data, 'json');
            return new JsonResponse($resultat, 403, [], true);
        }
    }

    /**
     * @Route("/cat/{id}", name="cat_delete", methods={"DELETE"})
     */
    public function deleteCat(Cat $cat): JsonResponse
    {
        if ($cat->getUser()->getId() === $this->getUser()->getId() || $this->getUser()->hasRoles(User::ROLE_ADMIN)) {
            $this->em->remove($cat);
            $this->em->flush();

            return new JsonResponse('', 204, [], true);
        } else {
            $data = [
                'message' => 'Accés refusé',
            ];
            $resultat = $this->serializer->serialize($data, 'json');
            return new JsonResponse($resultat, 403, [], true);
        }
    }

    //create - post

    //edit - put

    //delete - delete
    //Verifie proprietaire else 403 sauf admin
    //code http 204 si delete
    //Pourquoi 204 --> no content
}
