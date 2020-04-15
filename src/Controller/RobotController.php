<?php

namespace App\Controller;

use App\Entity\Robot;
use App\Form\RobotType;
use App\Repository\RobotRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RobotController extends AbstractController
{
    /**
     * @Route("/robot", name="robot")
     */
    public function index()
    {
        $robot = $this->getDoctrine()->getRepository(Robot::class)->findAll();

        return $this->render('robot/index.html.twig', [
            'fanuc' => $robot
        ]);
    }

    /** 
     * @Route("/robot/{id}", name="robot_edit")
     */
    public function edit(EntityManagerInterface $manager, Request $request, Robot $robot)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $form = $this->createForm(RobotType::class, $robot);

        $form->handleRequest($request);

        //si le formulaire a été soumis
        if($form->isSubmitted() && $form->isValid()) {
            //on enregistre le robot en bdd
            $manager = $this->getDoctrine()->getManager();

            //Inutile ici car l'objet provient déjà de la bdd
            //$manager->persist($robot);
            $manager->flush();

            $this->addFlash('success', 'Modification enregistré avec success !');

            return $this->redirectToRoute('robot');
        }

        return $this->render('robot/edit.html.twig', [
            'robot' => $robot,
            'form' => $form->createView()
        ]);
    }


}
