<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\RegistrationFormType;
use App\Form\UpdateParticipantFormType;
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
    public function updateDetails(int $id, EntityManagerInterface $entityManager, Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        //récupération du participant à modifier dans la bdd
        $participant= $entityManager->getRepository(Participant::class)->find($id);

        //gestion erreur
        if (!$participant){
            throw $this->createNotFoundException('Pas de participant trouvé');
        }

        $form=$this->createForm(RegistrationFormType::class, $participant);
        $form ->remove('plainPassword');


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($participant);
            $entityManager->flush();

            $debug = 'passage validé';
            dump($debug);

            $this->addFlash('success', 'Modification des informations réussie !');
            return $this->render('participant/details.html.twig', ["participant"=>$participant]);
        }

        $debug = 'passage non validé';
        dump($debug);

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('warning', 'Erreur dans le formulaire update');
        }

        return $this->render('participant/update.html.twig', [
            'registrationForm' => $form->createView(), "participant"=>$participant]);
    }

    /**
     * @Route("/participant/details/{id}/updatePassword", name="participant_updatePassword")
     */
    public function updatePassword(int $id, EntityManagerInterface $entityManager, Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        //récupération du participant à modifier dans la bdd
        $participant= $entityManager->getRepository(Participant::class)->find($id);

        //gestion erreur
        if (!$participant){
            throw $this->createNotFoundException('Pas de participant trouvé');
        }

        $form=$this->createForm(RegistrationFormType::class, $participant);
        //desactivation des champs non nécessaires
        $form->remove('nom');
        $form->remove('prenom');
        $form->remove('username');
        $form->remove('telephone');
        $form->remove('mail');
        $form->remove('campus');

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

            $this->addFlash('success', 'Modification mot de passe réussie !');
            return $this->render('participant/details.html.twig', ["participant"=>$participant]);
        }

        echo 'passage non valid';

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('warning', 'Erreur dans le formulaire du mot de passe');
        }

        return $this->render('participant/updatepassword.html.twig', [
            'registrationForm' => $form->createView(), "participant"=>$participant]);
    }

}
