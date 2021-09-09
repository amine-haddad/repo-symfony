<?php

namespace App\Controller;

use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
use App\Form\ProgramType;
use App\Service\Slugify;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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

                'numberPrograms' => count($programs) . " programs dans le catalogue",
                'website' => 'Wild Séries',
                'programs' => $programs
            ]
        );
    }

    /**
     * The controller for the category add form
     * 
     * @Route("/new", name="new")
     */
    public function new(Request $request, Slugify $slugify): Response
    {
        //create a new Program object
        $program = new Program();
        // Create the associated Form
        $form = $this->createForm(ProgramType::class, $program);

        // Get data from HTTP request
        $form->handleRequest($request);
        // Was the form is submitted?
        if ($form->isSubmitted() && $form->isValid()) {
            //Deal with the submitted data
            $slug = $slugify->generate($program->getTitle());
            $program->setSlug($slug);
            //Get the Entity Manager
            $entityManager = $this->getDoctrine()->getManager();
            //For exemple : persiste & flush the entity
            // Persist Category Object
            $entityManager->persist($program);
            //Flush the persisted object
            $entityManager->flush();
            //And redirect to a route that display the result
            return $this->redirectToRoute('program_index');
        }
        //Render the form
        return $this->render('program/new.html.twig', [
            "form" => $form->createView(),
        ]);
    }

    /**
     * @Route("/{slug}", name="show", methods={"GET"})
     * @ParamConverter("program", class="App\Entity\Program", options={"mapping": {"slug": "slug"}})
     * 
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
        $seasons = $programId->getSeasons();
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
     * @Route("/{slug}/season/{seasonId}", name="season_show", methods={"GET"})
     * @ParamConverter("program", class="App\Entity\Program", options={"mapping": {"slug": "slug"}})
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
        $episodes = $seasonId->getEpisodes();
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
     * @Route("/{slug}/season/{seasonId}/episodes/{slugy}", name="episode_show", methods={"GET"})
     * @ParamConverter("program", class="App\Entity\Program", options={"mapping": {"slug": "slug"}})
     * @ParamConverter("season", class="App\Entity\Season", options={"mapping": {"seasonId": "id"}})
     * @ParamConverter("episode", class="App\Entity\Episode", options={"mapping": {"slugy": "slug"}})
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

    /** 
     * @Route("/student/ajax") 
     */
    public function ajaxAction(Request $request)
    {
        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findAll();

        if ($request->isXmlHttpRequest() || $request->query->get('showJson') == 1) {
            $jsonData = array();
            $idx = 0;
            foreach ($programs as $program) {
                $temp = array(
                    'name' => $program->getName(),
                    'Actor' => $program->getActor(),
                );
                $jsonData[$idx++] = $temp;
            }
            return new JsonResponse($jsonData);
        } else {
            return $this->render('actor/index.html.twig');
        }
    }
}
