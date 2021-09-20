<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\RegistrationFormType;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ParticipantController extends AbstractController
{
    /**
     * @Route("/participant/details/{id}", name="participant_details")
     */
    public function detailsParticipant(int $id, ParticipantRepository $participantRepository): Response
    {
        $participant = $participantRepository->find($id);

        //gestion erreur
        if (!$participant){
            throw $this->createNotFoundException("participant inexistant !!");
        }

        return $this->render('participant/details.html.twig', ["participant"=>$participant]);
    }


    /**
     * @Route("/participant/details/{id}/update", name="participant_update")
     */
    /*
    public function updateDetails(int $id, Participant $participant, EntityManagerInterface $entityManager, Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        //récupération du participant à modifier dans la bdd
        $participant= $entityManager->getRepository(Participant::class)->find($id);

        //gestion erreur
        if (!$participant){
            throw $this->createNotFoundException('Pas de participant trouvé');
        }

        $form=$this->createForm(RegistrationFormType::class, $participant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $participant->setPassword(
                $passwordEncoder->encodePassword(
                    $participant,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($participant);
            $entityManager->flush();


            if ($form->isSubmitted() && !$form->isValid()) {
                $this->addFlash('warning', 'Erreur dans le formulaire');
            }

            return $this->render('registration/register.html.twig', [
                'registrationForm' => $form->createView()
            ]);
        }
    }
    */
}
