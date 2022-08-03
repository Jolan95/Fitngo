<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{


    #[Route(path: '/', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response

    
    {
        if ($this->getUser()) {
            
            $user = $this->getUser();
            $roles = $user->getRoles();
            if(in_array("ROLE_ADMIN", $roles)){
                return $this->redirectToRoute('app_admin');
            } else if (in_array("ROLE_STRUCTURE", $roles)){
                return $this->redirectToRoute('read_structure', ["token" =>$user->getUrl()]);
            } else{
                return $this->redirectToRoute("read_franchise", ["token" => $user->getUrl()]);
            }
            
         }
        
        $error = $authenticationUtils->getLastAuthenticationError();
        
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
