<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TwinController extends AbstractController
{
    /**
     * @Route("/twin", name="twin")
     */
    public function index()
    {
        # $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        return $this->render('twin/index.html.twig', [
            'controller_name' => 'TwinController',
        ]);
    }

    /**
     * @Route("/", name="home")
     */
    public function home()
    {
        return $this->render('twin/home.html.twig');
    }
}
