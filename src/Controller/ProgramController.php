<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
use App\Form\CommentType;
use App\Form\ProgramType;
use App\Form\SearchProgramFormType;
use App\Repository\ProgramRepository;
use App\Service\Slugify;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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
    public function index(Request $request, ProgramRepository $programRepository): Response
    {
        /*$programs = $this->getDoctrine()

            ->getRepository(Program::class)
            ->findAll();*/
            // Create the associated Form
            $form = $this->createForm(SearchProgramFormType::class);
            // Get data from HTTP request
            $form->handleRequest($request);
             // Was the form is submitted?
        if ($form->isSubmitted() && $form->isValid()){
            $search = $form->getData()['search'];
            $programs = $programRepository->findLikeName($search);
        }else{
            $programs= $programRepository->findAll();
        }
            
        
        return $this->render(
            'program/index.html.twig',
            [

                'numberPrograms' => count($programs) . " programs dans le catalogue",
                'website' => 'Wild Séries',
                'programs' => $programs,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * The controller for the category add form
     * 
     * @Route("/new", name="new")
     */
    public function new(Request $request, MailerInterface $mailer, Slugify $slugify): Response
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
            // Set the program's owner
            $program->setOwner($this->getUser());
            //For exemple : persiste & flush the entity
            // Persist Program Object
            $entityManager->persist($program);
            //Flush the persisted object
            $entityManager->flush();
            $email = (new Email())
                ->from($this->getParameter('mailer_from'))
                ->to('jo@jo.fr')
                ->subject('Une nouvelle série vient d\'être publiée !')
                ->html($this->renderView('program/newProgramEmail.html.twig', ['program' => $program]));
            $mailer->send($email);

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
     * @Route("/{slug}/season/{seasonId}/episodes/{slugy}", name="episode_show")
     * 
     * @ParamConverter("program", class="App\Entity\Program", options={"mapping": {"slug": "slug"}})
     * @ParamConverter("season", class="App\Entity\Season", options={"mapping": {"seasonId": "id"}})
     * @ParamConverter("episode", class="App\Entity\Episode", options={"mapping": {"slugy": "slug"}})
     * @return Response
     */
    public function showEpisode(Request $request, Program $program, Season $season, Episode $episode)
    {
        $comment = new Comment();

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
        $comments = $episode->getComments();
        
        $form = $this->createForm(CommentType::class, $comment);
        // Get data from HTTP request
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setAuthor($this->getUser());
            $comment->setEpisode($episode);
            $entityManager = $this->getDoctrine()->getManager();
            //For exemple : persiste & flush the entity
            // Persist Category Object
            $entityManager->persist($comment);
            //Flush the persisted object
            $entityManager->flush();

        }
        return $this->render('program/episode_show.html.twig', [
            'season' => $season,
            'program' => $program,
            'episode' => $episode,
            'comment' => $comment,
            'comments' => $comments,
            "form" => $form->createView(),
            //'user'=> $user
        ]);
    }
    /**
     * @Route("/{slug}/edit", name="edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Program $program): Response
    {
        // Check wether the logged in user is the owner of the program
        if (!($this->getUser() == $program->getOwner())) {
            // If not the owner, throws a 403 Access Denied exception
            throw new AccessDeniedException('Only the owner can edit the program!');
        }
        // ...
        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('program_index');
        }

        return $this->render('program/edit.html.twig', [
            'program' => $program,
            'form' => $form->createView(),
        ]);
    }

    

    /**
     * @Route("/{slug}", name="delete", methods={"POST"})
     * 
     */
    public function delete(Request $request, Program $program): Response
    {
        if ($this->isCsrfTokenValid('delete'.$program->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($program);
            $entityManager->flush();
        }

        return $this->redirectToRoute('program_index');
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
