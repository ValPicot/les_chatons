<?php


namespace App\Controller;


use App\Entity\Cat;
use App\Form\CatType;
use App\Repository\CatRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
     * @return Response
     */
    public function list() {
        $cats = $this->repository->findAll();
        return $this->render('base.html.twig', [
            'cats' => $cats
        ]);
    }

    /**
     * @Route("/create", name="create")
     * @param Request $request
     * @return Response
     */
    public function create(Request $request) {
        $cat = new Cat();
        $form = $this->createForm(CatType::class, $cat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($cat);
            $this->em->flush();
            $this->addFlash('success', 'Chat crée avec succès !');
            return $this->redirectToRoute('cats_list');
        }

        return $this->render('create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods="GET|POST")
     * @param Cat $cat
     * @param Request $request
     * @return Response
     */
    public function edit(Cat $cat, Request $request) {
        $form = $this->createForm(CatType::class, $cat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'Chat modifié avec succès !');
            return $this->redirectToRoute('cats_list');
        }

        return $this->render('edit.html.twig', [
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
            $this->addFlash('success', 'Chat supprimé avec succès !');
        }

        return $this->redirectToRoute('cats_list');
    }
}