<?php

namespace App\Controller;

use App\Data\SearchOptions;
use App\Entity\Sortie;
use App\Form\SortieFormType;
use App\Form\AnnulerSortieType;
use App\Form\SearchSortiesType;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use App\Services\CheckDeviceFromUser;
use App\Services\CheckEtatAndUpdate;
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
                           EntityManagerInterface $entityManager,
                            CheckDeviceFromUser $device,
                            CheckEtatAndUpdate $checkEtat): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $this->getUser();
        $userDevice = $device->checkDeviceFromUser();

        $data = new SearchOptions();
        $data->setCurrentUser($user);

        $form = $this->createForm(SearchSortiesType::class, $data);
        $form->handleRequest($request);

        if ($userDevice == 'isMobile') {
            $sorties = $sortieRepository->findSearchMobile($data, $userDevice);
        }
        else{
            $sorties = $sortieRepository->findSearch($data);
        }

        // change l'etat de la sortie en fonction de la date
        $checkEtat->checkEtatAndUpdate($sorties, $entityManager);

        if ($userDevice == 'isMobile') {
            return $this->render('sortie/list_sorties_mobile.html.twig', [
                'sorties' => $sorties,
                'searchSortiesForm' => $form->createView()
            ]);
        }
        else {
        return $this->render('sortie/list_sorties.html.twig', [
            'sorties' => $sorties,
            'searchSortiesForm' => $form->createView()
        ]);
        }
    }

    /**
     * @Route("/create/sortieForm", name="create_sortieForm")
     */
    public function createSortieForm(Request $request,
                                     EntityManagerInterface $entityManager,
                                     CheckDeviceFromUser $device): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->denyAccessUnlessGranted('ROLE_USER');
        $userDevice = $device->checkDeviceFromUser();

        if ($userDevice == 'isMobile') {
            return $this->redirectToRoute('sortie_search');
        }

        try {
            $user = $this->getUser()->getId();
            $sortie =new Sortie();
            $sortieForm= $this->createForm(SortieFormType::class, $sortie);


            $sortieForm->handleRequest($request);

            $participantActuelle = $entityManager->getReference('App:Participant',$user);
            $sortie->setParticipantOrganisateur($participantActuelle);

            $campusActuelle = $participantActuelle->getCampus();
            $sortie->setCampusOrganisateur($campusActuelle);

            if($sortieForm->isSubmitted() && $sortieForm->isValid()){
                $boutonValue = $request->get("bouton_creer");

                if($boutonValue == 'create'){
                    $etat = $entityManager->getReference('App:Etat','1');
                    $sortie->setEtat($etat);
                }
                elseif ($boutonValue == 'publish'){
                    $etat = $entityManager->getReference('App:Etat','2');
                    $sortie->setEtat($etat);
                }

                $entityManager->persist($sortie);
                $entityManager->flush();

                $this->addFlash('success', 'La sortie a bien ??t?? cr????');
                return $this->redirectToRoute('sortie_search');
            }


            return $this->render('sortie/create.html.twig', [
                'sortieForm' => $sortieForm->createView(),
            ]);
        } catch (\Exception $e) {
            $this->addFlash('warning', $e->getMessage());
            return $this->redirectToRoute('sortie_search');
        }
    }

    /**
     * @Route("/modifierSortie/{idSortie}_{idParticipant}", name="sortie_modifie")
     */
    public function modifierSortieForm(int $idParticipant, int $idSortie,
                                       Request $request,
                                       EntityManagerInterface $entityManager,
                                       CheckDeviceFromUser $device): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->denyAccessUnlessGranted('ROLE_USER');
        $userDevice = $device->checkDeviceFromUser();

        if ($userDevice == 'isMobile') {
            return $this->redirectToRoute('sortie_search');
        }

        try {

            $user = $this->getUser();
            $sortie = $entityManager->getRepository(Sortie::class)->find($idSortie);

            if ($sortie->getParticipantOrganisateur() !== $user and !$this->isGranted("ROLE_ADMIN")) {
                throw $this->createAccessDeniedException('Vous n\'??tes pas authoris?? ?? acc??der ?? cette page!');
            }

            $now = new \DateTime();
            if (($sortie->getEtat() !== '1' or $sortie->getEtat() !== '2') &&
                $now > $sortie->getDateHeureDebut() and !$this->isGranted("ROLE_ADMIN")) {
                throw $this->createAccessDeniedException('Cette sortie ne peut plus ??tre modifi??, veuillez contacter un admin');
            }

            $sortie= $entityManager->getRepository(Sortie::class)->find($idSortie);

            if (!$sortie){
                throw $this->createNotFoundException('Pas de sortie trouv??');
            }

            $sortieForm= $this->createForm(SortieFormType::class, $sortie);


            $sortieForm->handleRequest($request);

            if($sortieForm->isSubmitted() && $sortieForm->isValid()){
                $boutonValue = $request->get("bouton_modif");

                if($boutonValue == 'create'){
                    $etat = $entityManager->getReference('App:Etat','1');
                    $sortie->setEtat($etat);
                }
                elseif ($boutonValue == 'publish'){
                    $etat = $entityManager->getReference('App:Etat','2');
                    $sortie->setEtat($etat);
                }
                elseif ($boutonValue == 'supprimer'){
                    $etat = $entityManager->getReference('App:Etat','6');
                    $sortie->setEtat($etat);
                }


                $entityManager->persist($sortie);
                $entityManager->flush();

                $this->addFlash('success', 'La sortie a bien ??t?? modif??e');
                return $this->redirectToRoute('sortie_search');
            }

            return $this->render('sortie/modifSortie.html.twig', [
                'sortieForm' => $sortieForm->createView(),
                'sortie' => $sortie
            ]);

        } catch (\Exception $e) {
            $this->addFlash('warning', $e->getMessage());
            return $this->redirectToRoute('sortie_search');
        }
    }


    /**
     * @Route("/detailSortie/{idSortie}", name="sortie_detail")
     */
    public function detailSortie(int $idSortie,
                                 SortieRepository $sortieRepository,
                                 EntityManagerInterface $entityManager,
                                 CheckDeviceFromUser $device): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->denyAccessUnlessGranted('ROLE_USER');
        try {
            $sortie = $sortieRepository->find($idSortie);
            $userDevice = $device->checkDeviceFromUser();

            if ($userDevice == 'isMobile') {
                return $this->render('sortie/details-mobile.html.twig', [
                    'sortie' => $sortie,
                ]);
            }
            else {
                return $this->render('sortie/details.html.twig', [
                    'sortie' => $sortie
                ]);
            }
        }
        catch (\Exception $e) {
            $this->addFlash('warning', $e->getMessage());
            return $this->redirectToRoute('sortie_search');
        }
    }

    /**
     * @Route("/sortie/{idSortie}_{idParticipant}/inscription", name="sortie_inscription")
     */
    public function inscriptionSortie(int $idParticipant,int $idSortie,
                                      ParticipantRepository $participantRepository,
                                      EntityManagerInterface $entityManager,
                                      CheckDeviceFromUser $device): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->denyAccessUnlessGranted('ROLE_USER');

        try {
            //r??cup??ration instance Participant et Sortie
            $sortie = $entityManager->getRepository(Sortie::class)->find($idSortie);
            $userDevice = $device->checkDeviceFromUser();

            if ($userDevice == 'isMobile') {
                return $this->redirectToRoute('sortie_search');
            }


            //validation date de cloture
            $now = new \DateTime();
            if ($now >= $sortie->getDateLimiteInscription() || $sortie->getEtat()->getId() == 3) {
                if ($sortie->getEtat()->getId() != 3) {
                    $this->clotureInscriptionSortie($sortie, $entityManager);
                }
                $this->addFlash('warning', 'Inscription cl??tur??e! Date limite d\'inscription atteinte! ');
            } else {
                //validation nombre de participant max
                if ($sortie->getParticipantInscrit()->count() >= $sortie->getNbInscriptionsMax()) {
                    $this->addFlash('warning', 'Inscription cl??tur??e! Nombre de participant maximum atteint!');
                } else {
                    $participant = $participantRepository->find($idParticipant);
                    //validation indentification user
                    if ($this->getUser()->getUsername() == $participant->getUsername()) {
                        //insert BDD (table: participant_sortie)
                        $sortie->addParticipantInscrit($participant);
                        $entityManager = $this->getDoctrine()->getManager();
                        $entityManager->persist($participant);
                        $entityManager->flush();

                        $this->addFlash('success', 'Inscription ?? la sortie r??ussi! Amusez-vous bien!');
                    } else {
                        $this->addFlash('warning', 'Acc??s refus??! Vous ne pouvez pas inscrire quelqu\'un d\'autre!');
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
    public function seDesisterSortie(int $idParticipant,int $idSortie,
                                     ParticipantRepository $participantRepository,
                                     EntityManagerInterface $entityManager,
                                     CheckDeviceFromUser $device): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->denyAccessUnlessGranted('ROLE_USER');

        try {
            //r??cup??ration instance Participant et Sortie
            $sortie = $entityManager->getRepository(Sortie::class)->find($idSortie);
            $userDevice = $device->checkDeviceFromUser();

            if ($userDevice == 'isMobile') {
                return $this->redirectToRoute('sortie_search');
            }

            if ($sortie->getParticipantInscrit()->count() <= 0){
                $this->addFlash('warning', 'Aucun inscrit sur cette sortie');
                return $this->redirectToRoute('sortie_search');
            }
            else{
                $participant = $participantRepository->find($idParticipant);
                if ($this->getUser()->getUsername() == $participant->getUsername()) {
                    //insert BDD (table: participant_sortie)
                    $sortie->removeParticipantInscrit($participant);

                    if($sortie->getEtat()->getId() == '3' && $sortie->getDateLimiteInscription() > new \DateTime()){
                        $etat = $entityManager->getReference('App:Etat','2');
                        $sortie->setEtat($etat);
                    }
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($participant);
                    $entityManager->flush();

                    $this->addFlash('success', 'D??sistement valid??! ');
                }else{
                    $this->addFlash('warning', 'Acc??s refus??! Vous ne pouvez pas d??sinscrire quelqu\'un d\'autre!');
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
        EntityManagerInterface $entityManager,
        CheckDeviceFromUser $device): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->denyAccessUnlessGranted('ROLE_USER');

        try {

            $userDevice = $device->checkDeviceFromUser();

            if ($userDevice == 'isMobile') {
                return $this->redirectToRoute('sortie_search');
            }

            //r??cup??ration instance Sortie
            $sortie = $sortieRepository->find($idSortie);
            if ($this->getUser() !== $sortie->getParticipantOrganisateur() and !$this->isGranted("ROLE_ADMIN")) {
                throw $this->createAccessDeniedException('Vous n\'??tes pas authoris?? ?? acc??der ?? cette page!');
            }

            if ($sortie->getEtat()->getId() == '6') {
                throw $this->createAccessDeniedException('Cette sortie a d??j?? ??t?? annul??e!');
            }

            if ($sortie->getMotifAnnulation() !== null) {
                $sortie->setMotifAnnulation(null);
                $entityManager->persist($sortie);
                $entityManager->flush();
            }

            $form = $this->createForm(AnnulerSortieType::class, $sortie);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $etat = $entityManager->getReference('App:Etat', '6');
                $sortie->setEtat($etat);
                $sortie->setMotifAnnulation($form->get('motifAnnulation')->getData());
                $entityManager->persist($sortie);
                $entityManager->flush();

                $nomSortie = $sortie->getNom();
                $this->addFlash('success', "Sortie '$nomSortie' annul??e!");

                return $this->redirectToRoute('sortie_search');
            }

            return $this->render('sortie/annuler_sortie.html.twig', [
                'sortieAnnulee' => $sortie,
                'annuleeSortieForm' => $form->createView()
            ]);

        } catch (\Exception $e) {
            $this->addFlash('warning', $e->getMessage());
            return $this->redirectToRoute('sortie_search');
        }
    }


    private function clotureInscriptionSortie(Sortie $sortie, EntityManagerInterface $entityManager):void{
        //init var date actuelle
        $now = new \DateTime();
        //recherche date cl??ture et changement d'??tat si date atteinte
        if ($now >= $sortie->getDateLimiteInscription() && $sortie->getEtat()->getId()!=3){

            //mise ?? jour BDD
            $etat = $entityManager->getReference('App:Etat','3');
            $sortie->setEtat($etat);

            $entityManager->persist($sortie);
            $entityManager->flush();
        }
    }

}

