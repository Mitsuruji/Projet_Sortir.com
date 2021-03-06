<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\RegistrationFormType;
use App\Services\CheckDeviceFromUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints\Image;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request,
                             UserPasswordEncoderInterface $passwordEncoder,
                             CheckDeviceFromUser $device): Response
    {
        $userDevice = $device->checkDeviceFromUser($request);
        if ($userDevice == 'isMobile') {
            return $this->redirectToRoute('sortie_search');
        }

        $user = new Participant();
        $form = $this->createForm(RegistrationFormType::class, $user);

        //hydratation des champs hors formulaire
        $user->setRoles(["ROLE_USER"]);
        $user->setAdministrateur(false);
        $user->setActif(true);

        //gestion option inscription suivant ROLE_ADMIN
        $admin = $this->isGranted("ROLE_ADMIN");

        if (!$admin){
            //suppression de l'affichage d'options
            $form->remove('administrateur');
            $form->remove('actif');

            $form->handleRequest($request);
        }
        else{
            $form->handleRequest($request);

            //donne ROLE_ADMIN si case administrateur coché
            if ($form->get('administrateur')->getData() == true){
                $user->setRoles(["ROLE_ADMIN"]);
            }
            //enleve ROLE_USER si case actif non coché
            if ($form->get('actif')->getData() == false) {
                $user->setRoles(["ROLE_INACTIF"]);
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            //gestion image
            $photo = $form->get('photo')->getData();
            if ($photo){
                //genere un nouveau nom de fichier
                $fichier = md5(uniqid()).'.'.$photo->guessExtension();

                //copie de la photo dans le dossier uploads
                $photo->move($this->getParameter('images_directory'), $fichier);

                //envoie du nom de fichier dans la BDD
                $user->setPhoto($fichier);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email


            $this->addFlash('success', 'Enregistrement validé ! Bienvenue !');
            return $this->redirectToRoute('sortie_search');
        }

        if ($form->isSubmitted() && !$form->isValid()){
            $this->addFlash('warning', 'Erreur dans le formulaire');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

}
