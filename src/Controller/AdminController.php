<?php

namespace App\Controller;

use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin", name="admin_")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function dashBoard(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('admin/dashBoard.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("/listePatricipants", name="listePatricipants")
     */
    public function listeParticipants(ParticipantRepository $participantRepository): Response
    {
        try{
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
            $participants = $participantRepository->findAll();

            return $this->render('admin/listeParticipants.html.twig',[
                'participants' => $participants
            ]);
        }catch (\Exception $e){
            $this->addFlash('warning', $e->getMessage());
            return $this->render('admin/dashBoard.html.twig',[
                'participants' => $participants
            ]);
        }
    }

    /**
     * @Route("/supprimerParticipant/{id}", name="supprimerParticipant")
     */
    public function supprimerParticipant(int $id, ParticipantRepository $participantRepository, SortieRepository $sortieRepository): Response
    {
        try {
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            $this->denyAccessUnlessGranted('ROLE_ADMIN');

            $participant = $participantRepository->find($id);

            //suppression fichier photo dans dossier upload
            $photo = $participant->getPhoto();
            if ($photo) {
                //suppression du fichier de l'ancienne photo
                $nomphoto = $participant->getPhoto();
                if ($nomphoto) {
                    unlink($this->getParameter('images_directory') . '/' . $nomphoto);
                }
            }

            //récupération liste sorties participant inscrit
            $sortiesInscrit = $participant->getSortiesParticipations();
            if ($sortiesInscrit){
                foreach ($sortiesInscrit as $sortieInscrit){
                    //appel à la fonction seDesister() dans SortieController
                    $this->forward('App/controller/SortieController::seDesister()', [ $id, $sortieInscrit->getId(), $participantRepository]);
                }
            }

            //récupération liste sorties participant organisateur
            $sortiesOrganisateur = $sortieRepository->findBy(['participantOrganisateur'=>$participant->getId()] );
            //suppression de l'id organisateur dans chaque sortie (foreign key) et hydratation avec un utilisateur transistion
            if ($sortiesOrganisateur){
                $organisateur = $participantRepository->find(17);
                foreach ($sortiesOrganisateur as $sortieOrganisateur){
                    $sortieOrganisateur->setParticipantOrganisateur($organisateur);
                }
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($participant);
            $entityManager->flush();

            $this->addFlash('success', 'Suppression participant réussie!');
            return $this->render('admin/dashBoard.html.twig');
        }catch (\Exception $e){
            $this->addFlash('warning', $e->getMessage());
            return $this->render('admin/dashBoard.html.twig');
        }
    }

}
