<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/change_locale/{locale}", name="change_locale")
     */
    public function changeLocale(Request $request)
    {
        // On stocke la langue dans la session
        

        $request->getLocale();

        // On revient sur la page précédente
        return $this->redirect($request->headers->get('referer'));
    }
}
