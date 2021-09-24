<?php

namespace App\Controller;

use App\Data\SearchOptions;
use App\Entity\Sortie;
use App\Form\SortieFormType;
use App\Form\AnnulerSortieType;
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

    public function createSortieForm(Request $request, EntityManagerInterface $entityManager): Response
    {
        $sortie =new Sortie();
        $sortie->setDateCreated(new \DateTime());
        $sortieForm= $this.$this->createForm(SerieType::class, $sortie);

        $sortieForm->handleRequest($request);


        if($sortieForm->isSubmitted() && $sortieForm->isValid()){
            $entityManager->persist($sortie);
            $entityManager->flush();

            $this->addFlash('succes', 'La sortie a bien été créé');
            return $this->redirectToRoute('sortie_search');
        }

        return $this->render('sortie/create.html.twig', [
            'sortieForm'=>   $sortieForm->createView()

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


            if ($sorties->getParticipantInscrit()->count() >= $sorties->getNbInscriptionsMax()){
                $this->addFlash('warning', 'Sortie au complet, impossible de s\'inscrire!');
                return $this->render('main/home.html.twig');
            }
            else{
                $participant = $participantRepository->find($idParticipant);
                if ($this->getUser()->getUsername() == $participant->getUsername()){
                    //insert BDD (table: participant_sortie)
                    $sorties->addParticipantInscrit($participant);
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($participant);
                    $entityManager->flush();

                    $this->addFlash('success', 'Inscription à la sortie réussi! Amusez-vous bien!');
                    return $this->render('main/home.html.twig');
                }
                else{
                    $this->addFlash('warning', 'Erreur! Vous ne pouvez pas inscrire quelqu\'un d\'autre!');
                    return $this->render('main/home.html.twig');
                }
            }

        }catch (\Exception $e){
            $this->addFlash('warning', $e->getMessage());
            return $this->render('main/home.html.twig');
        }
    }

    /**
     * @Route("/sortie/{idSortie}_{idParticipant}/sedesister", name="sortie_se_desister")
     */
    public function seDesisterSortie(int $idParticipant,int $idSortie, ParticipantRepository $participantRepository, EntityManagerInterface $entityManager): Response
    {
        try {
            //récupération instance Participant et Sortie
            $sorties = $entityManager->getRepository(Sortie::class)->find($idSortie);

            if ($sorties->getParticipantInscrit()->count() <= 0){
                $this->addFlash('warning', 'Aucun inscrit sur cette sortie');
                return $this->render('main/home.html.twig');
            }
            else{
                $participant = $participantRepository->find($idParticipant);
                if ($this->getUser()->getUsername() == $participant->getUsername()) {
                    //insert BDD (table: participant_sortie)
                    $sorties->removeParticipantInscrit($participant);
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($participant);
                    $entityManager->flush();

                    $this->addFlash('success', 'Désistement validé ! ');
                    return $this->render('main/home.html.twig');
                }else{
                    $this->addFlash('warning', 'Erreur! Vous ne pouvez pas désinscrire quelqu\'un d\'autre!');
                    return $this->render('main/home.html.twig');
                }
            }

        }catch (\Exception $e){
            $this->addFlash('warning', $e->getMessage());
            return $this->render('main/home.html.twig');
        }
    }

    /**
     * @Route("/sortie/{idSortie}/annulation", name="sortie_annulation")
     */
    public function annulerSortie(
        int $idSortie,
        SortieRepository $sortieRepository,
        Request $request,
        EntityManagerInterface $entityManager): Response
    {
        try {
            //récupération instance Sortie
            $sortie = $sortieRepository->find($idSortie);

            if ($sortie->getMotifAnnulation() !== null){
                $sortie->setMotifAnnulation(null);
                $entityManager->persist($sortie);
                $entityManager->flush();
            }

            $form = $this->createForm(AnnulerSortieType::class, $sortie);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $etat = $entityManager->getReference('App:Etat','6');
                $sortie->setEtat($etat);
                $sortie->setMotifAnnulation($form->get('motifAnnulation')->getData());
                $entityManager->persist($sortie);
                $entityManager->flush();

                $nomSortie = $sortie->getNom();
                $this->addFlash('success', "Sortie '$nomSortie' annulée!");

                return $this->redirectToRoute('sortie_search');
            }

            return $this->render('sortie/annuler_sortie.html.twig', [
                'sortieAnnulee' => $sortie,
                'annuleeSortieForm' => $form->createView()
            ]);

        }catch (\Exception $e){
            $this->addFlash('warning', $e->getMessage());
            return $this->render('main/home.html.twig');
        }
    }


}
