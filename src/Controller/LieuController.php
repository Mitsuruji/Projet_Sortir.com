<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Data\SearchOptions;
use App\Form\LieuSortieFormType;
use App\Form\SearchSortiesType;
use App\Repository\LieuRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;

class LieuController extends AbstractController
{
//    /**
//     * @Route("/Lieu", name="lieu_search")
//     */
//    public function lieuSortieForm(LieuRepository $lieuRepository, Request $request): Response
//    {
//        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
//        $lieu = $this->getLieu();
//        $data = new Lieu();
//        $data->setCurrentLieu($lieu);
//
//        $form = $this->createForm(SearchSortiesType::class, $data);
//        $form->handleRequest($request);
//
//        $lieux = $lieuRepository->findSearch($data);
//        return $this->render('lieu/list_lieux.html.twig', [
//            'lieux' => $lieux,
//            'lieuSortieForm' => $form->createView()
//        ]);
//    }
//
}