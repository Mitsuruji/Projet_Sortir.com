<?php

namespace App\Controller;

use App\Data\SearchOptions;
use App\Entity\Sortie;
use App\Form\SearchSortiesType;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{
    /**
     * @Route("/", name="sortie_search")
     */
    public function sortie(SortieRepository $sortieRepository, Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $data = new SearchOptions();
        $data->setCurrentUser($user);

        $form = $this->createForm(SearchSortiesType::class, $data);
        $form->handleRequest($request);

        $sorties = $sortieRepository->findSearch($data);
        return $this->render('sortie/list_sorties.html.twig', [
            'sorties' => $sorties,
            'searchSortiesForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/sortie/{idSortie}_{idParticipant}/inscription", name="sortie_inscription")
     */
    public function inscriptionSortie(int $idParticipant,int $idSortie, ParticipantRepository $participantRepository, EntityManagerInterface $entityManager): Response
    {
        try {
            //récupération instance Participant et Sortie
            $sorties = $entityManager->getRepository(Sortie::class)->find($idSortie);
            $participant = $participantRepository->find($idParticipant);

            //insert BDD (table: participant_sortie)
            $sorties->addParticipantInscrit($participant);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($participant);
            $entityManager->flush();

            $this->addFlash('success', 'Inscription à la sortie réussi ! Amusez-vous bien !');
            return $this->render('main/home.html.twig');

        }catch (\Exception $e){
            $this->addFlash('warning', $e->getMessage());
            return $this->render('main/home.html.twig');
        }
    }
}
