<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\VilleSortieType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;


class VilleController extends AbstractController
{

    /**
     * @Route("/create/Ville", name="create_ville")
     */
    public function lieuSortieForm(Request $request, EntityManagerInterface $entityManager): Response
    {
        $ville =new Ville();
        $villeSortie= $this->createForm(VilleSortieType::class, $ville);

        $villeSortie->handleRequest($request);


        if($villeSortie->isSubmitted() && $villeSortie->isValid()){
            $entityManager->persist($ville);
            $entityManager->flush();

            $this->addFlash('success', 'La ville a bien été créé');
            return $this->render('sortie/create.html.twig');
        }

        return $this->render('lieu/create_lieu.html.twig', [
            'villeSortie'=>   $villeSortie->createView()

        ]);
    }
}