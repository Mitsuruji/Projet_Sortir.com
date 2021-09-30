<?php

namespace App\Controller;

use App\Services\CheckDeviceFromUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils,
                          CheckDeviceFromUser $device,
                          Request $request): Response
    {
        $userDevice = $device->checkDeviceFromUser($request);
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        if ($error){
            $this->addFlash('warning', 'Erreur de login');
        }

        if ($userDevice == 'isMobile') {
            return $this->render('security/login_mobile.html.twig',
                ['last_username' => $lastUsername, 'error' => $error]);
        }
        else {
            return $this->render('security/login.html.twig',
                ['last_username' => $lastUsername, 'error' => $error]);
        }
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
