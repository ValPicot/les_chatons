<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/cats", name="cats_")
 */
class ChatonsController extends AbstractController
{
    /**
     * @Route("/", name="list")
     * @return Response
     */
    public function list() {
        return $this->render('base.html.twig');
    }

    /**
     * @Route("/create", name="create")
     * @return Response
     */
    public function create() {
        return $this->render('create.html.twig');
    }
}