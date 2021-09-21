<?php

namespace App\Controller;

use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{
    /**
     * @Route("/listsortie", name="sortie")
     */
    public function sortie(SortieRepository $sortieRepository): Response
    {
        $sorties = $sortieRepository->findSearch();
        return $this->render('sortie/list_sorties.html.twig', [
            'sorties' => $sorties,
        ]);
    }
}
