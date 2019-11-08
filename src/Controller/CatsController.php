<?php


namespace App\Controller;


use App\Entity\Cat;
use App\Form\CatType;
use App\Repository\CatRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/cats", name="cats_")
 */
class CatsController extends AbstractController
{
    /**
     * @var CatRepository
     */
    private $repository;
    /**
     * @var ObjectManager
     */
    private $em;

    public function __construct(CatRepository $catRepository, ObjectManager $em){
        $this->repository = $catRepository;
        $this->em = $em;
    }

    /**
     * @Route("/", name="list")
     */
    public function list() : Response {
        $cats = $this->repository->findAll();
        return $this->render('cats/list.html.twig', [
            'cats' => $cats
        ]);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request) : Response {
        $cat = new Cat();
        $form = $this->createForm(CatType::class, $cat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($cat);
            $this->em->flush();
            $this->addFlash('success', 'flash.create.cat.success');
            return $this->redirectToRoute('cats_list');
        }

        return $this->render('cats/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods="GET|POST")
     */
    public function edit(Cat $cat, Request $request) : Response {
        $form = $this->createForm(CatType::class, $cat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'flash.edit.cat.success');
            return $this->redirectToRoute('cats_list');
        }

        return $this->render('cats/edit.html.twig', [
            'cat' => $cat,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/edit/{id}", name="delete", methods="DELETE")
     * @param Cat $cat
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(Cat $cat, Request $request) {
        if ($this->isCsrfTokenValid('delete' . $cat->getId(), $request->get('_token'))) {
            $this->em->remove($cat);
            $this->em->flush();
            $this->addFlash('success', 'flash.delete.cat.success');
        }

        return $this->redirectToRoute('cats_list');
    }
}