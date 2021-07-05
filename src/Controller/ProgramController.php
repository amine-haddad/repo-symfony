<?php

namespace App\Controller;

use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/programs", name="program_")
 */
class ProgramController extends AbstractController
{
    /**
     * Show all rows from Program’s entity
     * 
     * @Route("/", name="index")
     * @return Response A response instance
     */
    public function index(): Response
    {
        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findAll();
        return $this->render(
            'program/index.html.twig', [
            'website' => 'Wild Séries',
            'programs'=>$programs
        ]);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"},requirements={"id"="\d+"})
     * @return Response
     */
    public function show(int $id ): Response
    {
        $program = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findOneBy(['id' => $id]);
            if (!$program) {
                throw $this->createNotFoundException(
                    'No program with id : '.$id.' found in program\'s table.'
                ); 
            }
            $seasons = $this->getDoctrine()
            ->getRepository(Season::class)
            ->findAll();
            if (!$seasons) {
                throw $this->createNotFoundException(
                    'No program with id : '.$id.' found in program\'s table.'
                );               
            }    
        return $this->render('program/show.html.twig', [
            'id' => $id,
            'program' => $program,
            'seasons'=> $seasons
        ]);
    }

    /**
     * @Route("/{programId}/season/{seasonId}", name="season_show", methods={"GET"},requirements={"id"="\d+"})
     * @return Response
     */
    public function showSeason(int $programId, int $seasonId)
    {
        $program = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findOneBy(['id' => $programId]);
            if (!$program) {
                throw $this->createNotFoundException(
                    'No program with id : '.$programId.' found in program\'s table.'
                ); 
            } 
        $season = $this->getDoctrine()
            ->getRepository(Season::class)
            ->findOneBy(['id' => $seasonId]);
            if (!$season) {
                throw $this->createNotFoundException(
                    'No program with id : '.$seasonId.' found in program\'s table.'
                );               
            }
            $episodes = $this->getDoctrine()
            ->getRepository(Episode::class)
            ->findBy(['season'=> $season]);
            if (!$episodes) {
                throw $this->createNotFoundException(
                    'No program with id : '.$episodes.' found in program\'s table.'
                );               
            }    
            return $this->render('program/season_show.html.twig',[
            
                'season' => $season,
                'program' => $program,
                'episodes'=> $episodes
    
            ]);
    }
}
