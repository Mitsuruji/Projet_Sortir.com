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
    public function sortie(SortieRepository $sortieRepository,
                           Request $request,
                           EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $data = new SearchOptions();
        $data->setCurrentUser($user);

        $form = $this->createForm(SearchSortiesType::class, $data);
        $form->handleRequest($request);

        $sorties = $sortieRepository->findSearch($data);

        // change l'etat de la sortie en fonction de la date
        $now = new \DateTime();
        foreach ($sorties as $sortie){
            /* @var $sortie Sortie */

           if($sortie->getEtat()->getId() === 2 &&
               ($sortie->getParticipantInscrit()->count() >= $sortie->getNbInscriptionsMax() or
               $sortie->getDateLimiteInscription() < $now))
           {
               $etatCloture = $entityManager->getReference('App:Etat','3');
                $sortie->setEtat($etatCloture);
               $entityManager->persist($sortie);
               $entityManager->flush();
           }
           if (($sortie->getEtat()->getId() !== 1 or $sortie->getEtat()->getId() !== 5) &&
                $now > date_add($sortie->getDateHeureDebut(), date_interval_create_from_date_string("30 days")))
           {
               $etatArchive = $entityManager->getReference('App:Etat','5');
               $sortie->setEtat($etatArchive);
               $entityManager->persist($sortie);
               $entityManager->flush();
           }
        }

        return $this->render('sortie/list_sorties.html.twig', [
            'sorties' => $sorties,
            'searchSortiesForm' => $form->createView()
        ]);
    }

    /*
 * @Route("/create/sortieForm", name="create_sortieForm")
 */
    /*  en attente correction laurent
         public function createSortieForm(Request $request, EntityManagerInterface $entityManager): Response
           {
               $sortie =new Sortie();
               $sortie->setDateCreated(new \DateTime());
               $sortieForm= $this->createForm(SortieFormType::class, $sortie);

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
    */


    /**
     * @Route("/detailSortie/{idSortie}", name="sortie_detail")
     */
    public function detailSortie(int $idSortie, SortieRepository $sortieRepository,
                           Request $request,
                           EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        try {
            $sortie = $sortieRepository->find($idSortie);
            if ($sortie->getEtat()->getId() == '5') {
                throw $this->createAccessDeniedException('Cette sortie est archivée, vous n\'êtes pas authorisé à accéder à cette page!');
            }
            return $this->render('sortie/details.html.twig', [
                'sortie' => $sortie
            ]);
        }
        catch (\Exception $e) {
            $this->addFlash('warning', $e->getMessage());
            return $this->redirectToRoute('sortie_search');
        }
    }

    /**
     * @Route("/sortie/{idSortie}_{idParticipant}/inscription", name="sortie_inscription")
     */
    public function inscriptionSortie(int $idParticipant,int $idSortie, ParticipantRepository $participantRepository, EntityManagerInterface $entityManager): Response
    {
        try {
            //récupération instance Participant et Sortie
            $sortie = $entityManager->getRepository(Sortie::class)->find($idSortie);


            //validation date de cloture
            $now = new \DateTime();
            if ($now >= $sortie->getDateLimiteInscription() || $sortie->getEtat()->getId() == 3) {
                if ($sortie->getEtat()->getId() != 3) {
                    $this->clotureInscriptionSortie($sortie, $entityManager);
                }
                $this->addFlash('warning', 'Inscription clôturée! Date limite d\'inscription atteinte! ');
            } else {
                //validation nombre de participant max
                if ($sortie->getParticipantInscrit()->count() >= $sortie->getNbInscriptionsMax()) {
                    $this->addFlash('warning', 'Inscription clôturée! Nombre de participant maximum atteint!');
                } else {
                    $participant = $participantRepository->find($idParticipant);
                    //validation indentification user
                    if ($this->getUser()->getUsername() == $participant->getUsername()) {
                        //insert BDD (table: participant_sortie)
                        $sortie->addParticipantInscrit($participant);
                        $entityManager = $this->getDoctrine()->getManager();
                        $entityManager->persist($participant);
                        $entityManager->flush();

                        $this->addFlash('success', 'Inscription à la sortie réussi! Amusez-vous bien!');
                    } else {
                        $this->addFlash('warning', 'Accès refusé! Vous ne pouvez pas inscrire quelqu\'un d\'autre!');
                    }
                }
            }

            return $this->redirectToRoute('sortie_search');
        } catch (\Exception $e) {
            $this->addFlash('warning', $e->getMessage());
            return $this->redirectToRoute('sortie_search');
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
                return $this->redirectToRoute('sortie_search');;
            }
            else{
                $participant = $participantRepository->find($idParticipant);
                if ($this->getUser()->getUsername() == $participant->getUsername()) {
                    //insert BDD (table: participant_sortie)
                    $sorties->removeParticipantInscrit($participant);
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($participant);
                    $entityManager->flush();

                    $this->addFlash('success', 'Désistement validé! ');
                }else{
                    $this->addFlash('warning', 'Accès refusé! Vous ne pouvez pas désinscrire quelqu\'un d\'autre!');
                }
            }
            return $this->redirectToRoute('sortie_search');

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
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        try {
            //récupération instance Sortie
            $sortie = $sortieRepository->find($idSortie);
            if($this->getUser() !== $sortie->getParticipantOrganisateur()){
                throw $this->createAccessDeniedException('Vous n\'êtes pas authorisé à accéder à cette page!');
            }

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
            return $this->redirectToRoute('sortie_search');
        }
    }


    private function clotureInscriptionSortie(Sortie $sortie, EntityManagerInterface $entityManager):void{
        //init var date actuelle
        $now = new \DateTime();
        //recherche date clôture et changement d'état si date atteinte
        if ($now >= $sortie->getDateLimiteInscription() && $sortie->getEtat()->getId()!=3){

            //mise à jour BDD
            $etat = $entityManager->getReference('App:Etat','3');
            $sortie->setEtat($etat);

            $entityManager->persist($sortie);
            $entityManager->flush();
        }
    }

}

