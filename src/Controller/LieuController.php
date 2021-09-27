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
     * @Route("/create/Lieu", name="lieu_create")
     */
    public function lieuSortieForm(Request $request, EntityManagerInterface $entityManager): Response
    {
        $lieu =new Lieu();
        $lieuSortieForm= $this->createForm(LieuSortieFormType::class, $lieu);

        $lieuSortieForm->handleRequest($request);


        if($lieuSortieForm->isSubmitted() && $lieuSortieForm->isValid()){
            $entityManager->persist($lieu);
            $entityManager->flush();

            $this->addFlash('succes', 'Le lieu a bien été créé');
            return $this->render('sortie/create.html.twig');
        }

        return $this->render('lieu/create_lieu.html.twig', [
            'lieuSortieForm'=>   $lieuSortieForm->createView()

        ]);
    }

}