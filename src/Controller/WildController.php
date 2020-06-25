<?php

namespace App\Controller;

use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
use App\Entity\Category;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;



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
        
        
return $this->render('wild/index.html.twig', [
                'programs' => $programs,
                
            ]
);
    }
    /**
     *
     * @Route("wild/show/{slug}",requirements={"slug:<^[a-z0-9-]+$>"} , name="wild_show")
     * @return Response
     */
    public function show(?string $slug): Response
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
                "Aucun programme avec " . $slug . " n'a été trouvé"
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

        $categories = $this->getDoctrine()->getRepository(Category::class)->findOneBy(['name' => $categoryName]);

        if(!$categories)
        {
            throw $this->createNotFoundException('Aucune catégorie trouvé avec'. $categories);
        }

        $idCategory = $categories->getId();

        $Programs = $this->getDoctrine()
        ->getRepository(Program::class)->findBy(['category' => $idCategory],
            ['id' => 'desc'],
            3,
            0);

        return $this->render('wild/category.html.twig', ['Programs'=> $Programs]);

    }
    /**
     *@Route("wild/program/{programName}",requirements={"programName:<^[a-z0-9-]+$>"} , name="show_program")
     *@param string $programName   
     * @return Response
     */
    public function showByProgram(?string $programName): Response
    {
        $programName = preg_replace(
            '/-/',
            ' ', ucwords(trim(strip_tags($programName)), "-"));

        $program = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findOneBy(["title" => mb_strtolower($programName)]);

        if (!$programName)
         {
            throw $this->createNotFoundException("Aucun programme avec " . $programName . " n'a été trouvé");
         }
            
        
        $seasons = $program->getSeasons();

        return $this->render('wild/program.html.twig', [
            'program' => $program,
            'seasons' => $seasons,
        ]);
    }

    /**
     *@Route("wild/program/season/{id}", name="show_season").
     *@param int $id
     */
    public function showBySeason(?int $id): Response
    {
        if (!$id)
        {
            throw $this->createNotFoundException("Aucune saisons correspondantes n'a été trouvées.");
        }
        $season = $this->getDoctrine()
            ->getRepository(Season::class)
            ->find($id);

        $program = $season->getProgram();
        $episode = $season->getEpisodes();
        
        
        
        return $this->render('wild/season.html.twig',
        ['season'=>$season,
        'program'=>$program,
        'episode'=>$episode]);
        
    }

    /**
     * @Route("wild/program/episode/{id}", name="show_episode").
     * @param Episode $episode
     */
    public function showEpisode(Episode $episode)
    {
        if (!$episode)
        {
            throw $this->createNotFoundException("Aucun épisode n'a été trouvé");
        }
        $season = $episode->getSeason();
        $program = $season->getProgram();

        return $this->render('wild/episode.html.twig',
         ['season'=>$season,
         'program' => $program,
         'episode'=>$episode]);
    }


}

