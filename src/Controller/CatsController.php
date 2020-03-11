<?php

namespace App\Controller;

use App\Entity\Cat;
use App\Form\Type\CatType;
use App\Repository\CatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/cats", name="cats_")
 */
class CatsController extends AbstractController
{
    /**
     * @var CatRepository
     */
    private $catRepository;
    /**
     * @var EntityManagerInterface
     */
    private $em;

    private $security;

    public function __construct(CatRepository $catRepository, EntityManagerInterface $em, Security $security)
    {
        $this->catRepository = $catRepository;
        $this->em = $em;
        $this->security = $security;
    }

    /**
     * @Route("/", name="list")
     */
    public function list(PaginatorInterface $paginator, Request $request): Response
    {
        $cats = $paginator->paginate(
            $this->catRepository->findVisibleCats($this->getUser()),
            $request->query->getInt('page', 1),
            15
        );

        return $this->render('cats/list.html.twig', [
            'cats' => $cats,
        ]);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request): Response
    {
        $cat = new Cat();
        $cat->setUser($this->getUser());
        $form = $this->createForm(CatType::class, $cat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($cat);
            $this->em->flush();
            $this->addFlash('success', 'flash.create.cat.success');

            return $this->redirectToRoute('cats_list');
        }

        return $this->render('cats/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods="GET|POST")
     */
    public function edit(Cat $cat, Request $request, LoggerInterface $logger): Response
    {
        $form = $this->createForm(CatType::class, $cat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'flash.edit.cat.success');
            $logger->info('Un chat à été crée ! ');

            return $this->redirectToRoute('cats_list');
        }

        return $this->render('cats/edit.html.twig', [
            'cat' => $cat,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit/{id}", name="delete", methods="DELETE")
     *
     * @return RedirectResponse
     */
    public function delete(Cat $cat, Request $request)
    {
        if ($this->isCsrfTokenValid('delete'.$cat->getId(), $request->get('_token'))) {
            $this->em->remove($cat);
            $this->em->flush();
            $this->addFlash('success', 'flash.delete.cat.success');
        }

        return $this->redirectToRoute('cats_list');
    }
}
