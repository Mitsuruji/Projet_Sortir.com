<?php

namespace App\Controller;


use App\Data\SearchOptions;
use App\Form\LieuSortieFormType;
use App\Repository\LieuRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LieuController extends AbstractController
{
    /**
     * @Route("/Lieu", name="lieu_search")
     */
   /* en attente correction Laurent

     public function lieuSortieForm(LieuRepository $lieuRepository, Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $lieu = $this->getLieuSortie();
        $data = new SearchOptions();
        $data->setCurrentLieu($lieu);

        $form = $this->createForm(LieuSortieFormType::class, $data);
        $form->handleRequest($request);

        $lieux = $lieuRepository->findSearch($data);
        return $this->render('lieu/list_lieux.html.twig', [
            'lieux' => $lieux,
            'lieuSortieForm' => $form->createView()
        ]);
    }
   */

}