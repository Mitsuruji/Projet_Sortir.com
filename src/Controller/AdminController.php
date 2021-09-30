<?php

namespace App\Controller;

use App\Repository\ParticipantRepository;
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
    public function supprimerParticipant(int $id, ParticipantRepository $participantRepository): Response
    {
        try {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
            $participant = $participantRepository->find($id);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($participant);
            $entityManager->flush();

            $this->addFlash('success', 'Suppression participant réussie !');
            return $this->render('admin/dashBoard.html.twig');
        }catch (\Exception $e){
            $this->addFlash('warning', $e->getMessage());
            return $this->render('admin/dashBoard.html.twig');
        }
    }

}
