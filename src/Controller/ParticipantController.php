<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\RegistrationFormType;
use App\Repository\ParticipantRepository;
use App\Services\CheckDeviceFromUser;
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
    public function detailsParticipant(int $id,
                                       ParticipantRepository $participantRepository,
                                       CheckDeviceFromUser $device): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->denyAccessUnlessGranted('ROLE_USER');

        $userDevice = $device->checkDeviceFromUser();

        if ($userDevice == 'isMobile') {
            return $this->redirectToRoute('sortie_search');
        }
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
    public function updateDetails(int $id,
                                  EntityManagerInterface $entityManager,
                                  Request $request,
                                  CheckDeviceFromUser $device): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->denyAccessUnlessGranted('ROLE_USER');

        $userDevice = $device->checkDeviceFromUser();

        if ($userDevice == 'isMobile') {
            return $this->redirectToRoute('sortie_search');
        }

        //r??cup??ration du participant ?? modifier dans la bdd
        $participant= $entityManager->getRepository(Participant::class)->find($id);

        //gestion erreur
        if (!$participant){
            throw $this->createNotFoundException('Pas de participant trouv??');
        }

        $admin = $this->isGranted("ROLE_ADMIN");

        if ($this->getUser()->getUsername() == $participant->getUsername() or $admin){
            $form=$this->createForm(RegistrationFormType::class, $participant);
            $form ->remove('plainPassword');

            //gestion option inscription suivant ROLE_ADMIN
            if ($admin){
                $form->handleRequest($request);

                //donne role admin au nouvel inscrit si case admin coch??
                if ($form->get('administrateur')->getData() == true){
                    $participant->setRoles(["ROLE_ADMIN"]);
                }
                if ($form->get('actif')->getData() == false) {
                    $participant->setRoles(["ROLE_INACTIF"]);
                }
                else{
                    $participant->setRoles(["ROLE_USER"]);
                }
            }
            else{
                $form->remove('administrateur');
                $form->remove('actif');
                $form->remove('nom');
                $form->remove('prenom');
                $form->remove('mail');
                $form->handleRequest($request);
            }

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();

                //gestion image
                $photo = $form->get('photo')->getData();
                if ($photo){
                    //suppression de l'ancienne photo
                    $nomphoto = $participant->getPhoto();
                    if ($nomphoto){
                        unlink($this->getParameter('images_directory').'/'.$nomphoto);
                    }

                    //genere un nouveau nom de fichier
                    $fichier = md5(uniqid()).'.'.$photo->guessExtension();

                    //copie de la photo dans le dossier uploads
                    $photo->move($this->getParameter('images_directory'), $fichier);

                    //envoie du nom de fichier dans la BDD
                    $participant->setPhoto($fichier);
                }

                $entityManager->persist($participant);
                $entityManager->flush();

                $this->addFlash('success', 'Modification des informations r??ussie !');
                return $this->render('participant/details.html.twig', ["participant"=>$participant]);
            }

            if ($form->isSubmitted() && !$form->isValid()) {
                $this->addFlash('warning', 'Erreur dans le formulaire update');
            }
        }
        else{
            $this->addFlash('warning', 'Vous ne pouvez pas modifier les informations d\'un autre participant');
            return $this->render('participant/details.html.twig', ["participant"=>$participant]);
        }

        return $this->render('participant/update.html.twig', [
            'registrationForm' => $form->createView(), "participant"=>$participant]);
    }

    /**
     * @Route("/participant/details/{id}/updatePassword", name="participant_updatePassword")
     */
    public function updatePassword(int $id,
                                   EntityManagerInterface $entityManager,
                                   Request $request,
                                   UserPasswordEncoderInterface $passwordEncoder,
                                   CheckDeviceFromUser $device): Response
    {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->denyAccessUnlessGranted('ROLE_USER');

        $userDevice = $device->checkDeviceFromUser();

        if ($userDevice == 'isMobile') {
            return $this->redirectToRoute('sortie_search');
        }


        //r??cup??ration du participant ?? modifier dans la bdd
        $participant= $entityManager->getRepository(Participant::class)->find($id);

        //gestion erreur
        if (!$participant){
            throw $this->createNotFoundException('Pas de participant trouv??');
        }

        if ($this->getUser()->getUsername() == $participant->getUsername()){

            $form=$this->createForm(RegistrationFormType::class, $participant);

            //desactivation des champs non n??cessaires
            $form->remove('nom');
            $form->remove('prenom');
            $form->remove('username');
            $form->remove('telephone');
            $form->remove('mail');
            $form->remove('campus');
            $form->remove('photo');
            $form->remove('administrateur');
            $form->remove('actif');

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

                $this->addFlash('success', 'Modification mot de passe r??ussie !');
                return $this->render('participant/details.html.twig', ["participant"=>$participant]);
            }

            if ($form->isSubmitted() && !$form->isValid()) {
                $this->addFlash('warning', 'Erreur dans le formulaire du mot de passe');
            }
        }
        else{
            $this->addFlash('warning', 'Vous ne pouvez pas modifier le mot de passe d\'un autre participant');
            return $this->render('participant/details.html.twig', ["participant"=>$participant]);
        }

        return $this->render('participant/updatepassword.html.twig', [
            'registrationForm' => $form->createView(), "participant"=>$participant]);
    }

    /**
     * @Route("/participant/details/{id}/supprimerPhoto", name="participant_supprimerPhoto")
     */
    public function supprimerPhoto(int $id,
                                   ParticipantRepository $participantRepository,
                                   CheckDeviceFromUser $device): Response
    {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->denyAccessUnlessGranted('ROLE_USER');

        $userDevice = $device->checkDeviceFromUser();

        if ($userDevice == 'isMobile') {
            return $this->redirectToRoute('sortie_search');
        }

        $participant = $participantRepository->find($id);

        //gestion erreur
        if (!$participant){
            throw $this->createNotFoundException("participant inexistant !!");
        }

        if ($this->getUser()->getUsername() == $participant->getUsername() or $this->isGranted("ROLE_ADMIN")) {
            try {
                $photo = $participant->getPhoto();
                if ($photo) {
                    //suppression du fichier de l'ancienne photo
                    $nomphoto = $participant->getPhoto();
                    if ($nomphoto) {
                        unlink($this->getParameter('images_directory') . '/' . $nomphoto);
                    }

                    //suppression de la photo dans la bdd
                    $participant->setPhoto(null);
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($participant);
                    $entityManager->flush();

                    $this->addFlash('success', 'Suppression de la photo r??ussie !');
                    return $this->render('participant/details.html.twig', ["participant" => $participant]);
                }
                else{
                    $this->addFlash('warning', 'Pas de photo ?? supprimer');
                    return $this->render('participant/details.html.twig', ["participant" => $participant]);
                }
            } catch (\Exception $e){
                $this->addFlash('warning', $e->getMessage());
                return $this->render('participant/details.html.twig', ["participant" => $participant]);
            }
        }

        return $this->render('participant/details.html.twig', ["participant"=>$participant]);
    }


}
