<?php

namespace App\Controller;


use App\Entity\Lieu;
use App\Controller\VilleController;
use App\Form\LieuSortieFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class LieuController extends AbstractController
{
    /**
     * @Route("/create/Lieu", name="create_lieu")
     */
    public function lieuSortieForm(Request $request, EntityManagerInterface $entityManager): Response
    {
        $lieu =new Lieu();
        $lieuSortieForm= $this->createForm(LieuSortieFormType::class, $lieu);

        $lieuSortieForm->handleRequest($request);


        if($lieuSortieForm->isSubmitted() && $lieuSortieForm->isValid()){
            $entityManager->persist($lieu);
            $entityManager->flush();

            $this->addFlash('success', 'Le lieu a bien été créé');
            return $this->render('sortie/create.html.twig');
        }

        return $this->render('lieu/create_lieu.html.twig', [
            'lieuSortieForm'=>   $lieuSortieForm->createView()

        ]);
    }

}