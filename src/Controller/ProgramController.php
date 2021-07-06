<?php

namespace App\Controller;

use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
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
            'program/index.html.twig',
            [

                'website' => 'Wild Séries',
                'programs' => $programs
            ]
        );
    }

    /**
     * @Route("/{programId}", name="show", methods={"GET"},requirements={"id"="\d+"})
     * @ParamConverter("program", class="App\Entity\Program", options={"mapping": {"programId": "id"}})
     * 
     * @return Response
     */
    public function show(Program $programId): Response
    {;
        if (!$programId) {
            throw $this->createNotFoundException(
                'No program with id : ' . $programId . ' found in program\'s table.'
            );
        }
        $seasons = $this->getDoctrine()
            ->getRepository(Season::class)
            ->findAll();
        if (!$seasons) {
            throw $this->createNotFoundException(
                'No program with id : ' . $seasons . ' found in program\'s table.'
            );
        }
        return $this->render('program/show.html.twig', [

            'program' => $programId,
            'seasons' => $seasons
        ]);
    }

    /**
     * @Route("/{programId}/season/{seasonId}", name="season_show", methods={"GET"},requirements={"id"="\d+"})
     * @ParamConverter("program", class="App\Entity\Program", options={"mapping": {"programId": "id"}})
     * @ParamConverter("season", class="App\Entity\Season", options={"mapping": {"seasonId": "id"}})
     * @return Response
     */
    public function showSeason(Program $programId, Season $seasonId)
    {
        if (!$programId) {
            throw $this->createNotFoundException(
                'No program with id : ' . $programId . ' found in program\'s table.'
            );
        }
        if (!$seasonId) {
            throw $this->createNotFoundException(
                'No season with id : ' . $seasonId . ' found in season\'s table.'
            );
        }
        $episodes = $this->getDoctrine()
            ->getRepository(Episode::class)
            ->findBy(['season' => $seasonId]);
        if (!$episodes) {
            throw $this->createNotFoundException(
                'No episode with id : ' . $episodes . ' found in episode\'s table.'
            );
        }
        return $this->render('program/season_show.html.twig', [
            'season' => $seasonId,
            'program' => $programId,
            'episodes' => $episodes
        ]);
    }

    /**
     * @Route("/{programId}/season/{seasonId}/episodes/{episodeId}", name="episode_show", methods={"GET"},requirements={"id"="\d+"})
     * @ParamConverter("program", class="App\Entity\Program", options={"mapping": {"programId": "id"}})
     * @ParamConverter("season", class="App\Entity\Season", options={"mapping": {"seasonId": "id"}})
     * @ParamConverter("episode", class="App\Entity\Episode", options={"mapping": {"episodeId": "id"}})
     * @return Response
     */
    public function showEpisode(Program $program, Season $season, Episode $episode)
    {
        if (!$program) {
            throw $this->createNotFoundException(
                'No program with id : ' . $program . ' found in program\'s table.'
            );
        }
        if (!$season) {
            throw $this->createNotFoundException(
                'No season with id : ' . $season . ' found in season\'s table.'
            );
        }
        
        if (!$episode) {
            throw $this->createNotFoundException(
                'No episode with id : ' . $episode . ' found in episode\'s table.'
            );
        }
        return $this->render('program/episode_show.html.twig', [
            'season' => $season,
            'program' => $program,
            'episodes' => $episode
        ]);
    }
}
