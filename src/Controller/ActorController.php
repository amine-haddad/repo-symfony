<?php

namespace App\Controller;

use App\Entity\Actor;
use App\Entity\Program;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/actor", name="actor_")
 */
class ActorController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        $actors = $this->getDoctrine()
            ->getRepository(Actor::class)
            ->findAll();
            $programs =$this->getDoctrine()
            ->getRepository(Program::class)
            ->findAll();
        return $this->render('actor/index.html.twig', [
            'controller_name' => 'ActorController',
            'actors'=> $actors,
            'programs'=> $programs
        ]);
    }
    /**
     * @Route("/{id}", name="show")
     */
    public function show(Actor $actor){
        $programs = $actor->getPrograms();
        return $this->render('actor/show.html.twig', [
            'actor' => $actor,
            'programs'=>$programs

        ]);
    }
}
