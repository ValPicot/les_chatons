<?php

namespace App\Controller;

use App\Entity\Cat;
use App\Entity\User;
use App\Form\Type\ApiCatType;
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

    /**
     * @Route("/cat", name="cat_create", methods={"POST"})
     */
    public function createCat(Request $request)
    {
        $cat = new Cat();
        $cat->setUser($this->getUser());
        $form = $this->createForm(ApiCatType::class, $cat);
        $data = json_decode($request->getContent(), true);
        $form->submit($data);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($cat);
            $em->flush();

            $data = [
                'message' => 'Chat crée',
            ];
            $resultat = $this->serializer->serialize($data, 'json');

            return new JsonResponse($resultat, 201, [], true);
        }
        $resultat = $this->serializer->serialize($form->getErrors(), 'json');

        return new JsonResponse($resultat, 403, [], true);
    }

    /**
     * @Route("/cat/{id}", name="cat_edit", methods={"PUT"})
     */
    public function editCat(Cat $cat, Request $request)
    {
        $form = $this->createForm(ApiCatType::class, $cat);
        $data = json_decode($request->getContent(), true);
        $form->submit($data);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($cat);
            $em->flush();

            $data = [
                'message' => 'Chat modifié',
            ];
            $resultat = $this->serializer->serialize($data, 'json');

            return new JsonResponse($resultat, 200, [], true);
        }
        $resultat = $this->serializer->serialize($form->getErrors(), 'json');

        return new JsonResponse($resultat, 403, [], true);
    }
}
