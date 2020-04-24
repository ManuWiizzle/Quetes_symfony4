<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class WildController extends AbstractController
{
    /**
     * @Route("/wild", name="wild")
     */
    public function index()
    {
        return $this->render('wild/index.html.twig', [
            'controller_name' => 'WildController',
        ]);
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
        $slug = str_replace("-"," ",$slug);
        $slug = ucwords($slug);
        return $this->render('wild/show.html.twig',[ 'slug' => $slug]);

    }
}
