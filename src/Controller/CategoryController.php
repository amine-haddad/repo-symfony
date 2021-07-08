<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Program;
use App\Form\CategoryType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/categories", name="category_")
 */
class CategoryController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        $categories = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findAll();
        return $this->render(
            'category/index.html.twig',
            [
                'website' => 'Wild Séries',
                'categories' => $categories
            ]
        );
        
    }
    /**
     * The controller for the category add form
     * 
     * @Route("/new", name="new")
     */
    public function new(Request $request):Response
    {
        //create a new Category object
        $category = new Category();
        // Create the associated Form
        $form = $this->createForm(CategoryType::class, $category);
        // Get data from HTTP request
        $form->handleRequest($request);
        // Was the form is submitted?
        if($form->isSubmitted()&& $form->isValid()){
            //Deal with the submitted data
            //Get the Entity Manager
            $entityManager = $this->getDoctrine()->getManager();
            //For exemple : persiste & flush the entity
            // Persist Category Object
            $entityManager->persist($category);
            //Flush the persisted object
            $entityManager->flush();
            //And redirect to a route that display the result
            return $this->redirectToRoute('category_index');
        }
        //Render the form
        return $this->render('category/new.html.twig', [
            "form"=>$form->createView(),
        ]);
    }

    /**
     * @Route("/{categoryName}", name="show")
     * @return Response
     */
    public function show(string $categoryName): Response
    {
        $categoryName = $this->getDoctrine()
        ->getRepository(Category::class)
        ->findBy(['name' => $categoryName]);
        if (!$categoryName) {
            throw $this->createNotFoundException(
                'No categories with categoryName : '.$categoryName.' found in category\'s table.'
            );
        }
        $programCategory = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findBy(['category' => $categoryName],['id'=> 'ASC'],3,0);
        return $this->render('category/show.html.twig',[
            
            'categoryName' => $categoryName,
            'programCategory' => $programCategory

        ]);

    }
}
