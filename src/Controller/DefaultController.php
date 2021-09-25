<?php
namespace App\Controller;

use App\Entity\Program;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\Mapping\OrderBy;
use Doctrine\ORM\Mapping as ORM;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="app_index")
     * 
     */
    public function findLastProgramAdd(): Response
    {
        $programs = $this->getDoctrine()

        ->getRepository(Program::class)
        ->findAll();
        return $this->render('index.html.twig',[
            'programs'=>$programs
        ]);
    }
}