<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Program;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


class WildController extends AbstractController
{
    /**
     * @Route("/wild", name="wild_index")
     */
    public function index()
    {
        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findAll();

        if (!$programs) {
            throw $this->createNotFoundException(
                'No program found in program\'s table.'
            );
        }

        return $this->render(
            'wild/index.html.twig',
            ['programs' => $programs]
        );
    }
    /**
     * @Route("/wild/show/{slug<^[a-z0-9-]+$>}",defaults={"slug"= null}, name="wild_show")
     */
    public function show(?string $slug)
    {
        if (!$slug)
        {
            throw $this->createNotFoundException("Aucune série sélectionnée, veuillez choisir une série");
        }
        $slug = preg_replace(
            '/-/',
            ' ', ucwords(trim(strip_tags($slug)), "-")
        );
        $program = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findOneBy(['title' => mb_strtolower($slug)]);

        if (!$program) {
            throw $this->createNotFoundException(
                'No program with '.$slug.' title, found in program\'s table.'
            );
        }

        return $this->render('wild/show.html.twig', [
            'program' => $program,
            'slug'  => $slug,
        ]);
    }
    /**
     * @Route("wild/category/{categoryName}", name="show_category").
     */
    public function showByCategory(string $categoryName)
    {

        $repositoryCategory = $this->getDoctrine()->getRepository(Category::class);

        $category_str = $repositoryCategory->findOneBy(['name' => $categoryName]);

        if(!$category_str)
        {
            throw $this->createNotFoundException('Aucune catégorie trouvé avec'. $category_str);
        }

        $idCategory = $category_str->getId();

        $repositoryProgram = $this->getDoctrine()
            ->getRepository(Program::class);

        $Programs = $repositoryProgram->findBy(['category' => $idCategory],
            ['id' => 'desc'],
            3,
            0);

        return $this->render('wild/category.html.twig', ['Programs'=> $Programs]);




    }


}

