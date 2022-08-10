<?php

namespace App\Controller;

use App\Entity\User;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Repository\FranchiseRepository;
use App\Repository\StructureRepository;
use App\Repository\UserRepository;
use App\Form\PasswordType;
use App\Form\PasswordResetType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class SecurityController extends AbstractController
{


    #[Route(path: '/', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils,ManagerRegistry $manager )
    {
            
        if ($this->getUser()) {
            
            $user = $this->getUser();
            $roles = $user->getRoles();
            $structure = $user->getStructure();
            $franchise = $user->getFranchise();            
            
            if(in_array("ROLE_ADMIN", $roles)){

                return $this->redirectToRoute('app_admin');

            } else if (in_array("ROLE_STRUCTURE", $roles)){

                if($structure->getLastConnection() == null){
                    return $this->redirectToRoute("edit-password-structure", ['id' => $structure->getId()]);
                } 
                if(!$structure->isIsActive()){
                    return $this->render('read-only/acces-denied.html.twig', [ 'error' => "Votre structure est désactivé, Fitn'go ne vous donne pas d'accès pour le moment."]);
                } 
                $structure->setLastConnection(new \DateTime('now'));
                $entityManager = $manager->getManager();
                $entityManager->persist($structure);
                $entityManager->flush();
                return $this->redirectToRoute('read_structure', ["token" =>$user->getUrl()]);
                
            } else{

                if($franchise->getLastConnection() == null){
                    return $this->redirectToRoute("edit-password-franchise", ['id' => $franchise->getId()]);
                }
                if(!$franchise->isIsActive()){
                    return $this->render('read-only/acces-denied.html.twig', [ 'error' => "Votre franchise est désactivé, Fitn'go ne vous donne pas d'accès pour le moment."]);
                }
                $franchise->setLastConnection(new \DateTime('now'));
                $entityManager = $manager->getManager();
                $entityManager->persist($franchise);
                $entityManager->flush();
                return $this->redirectToRoute("read_franchise", ["token" => $user->getUrl()]);
            
            }
            
         }
        
        $error = $authenticationUtils->getLastAuthenticationError();
        
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/edit-my-password/{id}", name="edit-password-franchise")
     */
    public function edit_password_franchise($id,Request $request,ManagerRegistry $manager ,FranchiseRepository $franchiseRepository, UserRepository $userRepository,  UserPasswordHasherInterface $passwordHasher){

        $franchise = $franchiseRepository->findOneBy(["id" => $id]);
        $user = $userRepository->findOneBy(["Franchise" => $franchise]);

        $form = $this->createForm(PasswordResetType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $password = $form->getData()["password"];
            $hashedPassword = $passwordHasher->hashPassword($user, $password);
            $user->setPassword($hashedPassword);
            $franchise->setLastConnection(new \DateTime('now'));
            $entityManager = $manager->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            $entityManager = $manager->getManager();
            $entityManager->persist($franchise);
            $entityManager->flush();
            $this->addFlash("success","Ton mot de passe à bien été modifié");
        }
        
        return $this->render("security/edit-password.html.twig", [
            'message' => "It's good franchise",
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/edit-my-password/structure/{id}", name="edit-password-structure")
     */
    public function edit_password_structure($id, Request $request,StructureRepository $structureRepository, UserRepository $userRepository, ManagerRegistry $manager, UserPasswordHasherInterface $passwordHasher){

        $structure = $structureRepository->findOneBy(["id" => $id]);
        $user = $userRepository->findOneBy(["Structure" => $structure]);

        $form = $this->createForm(PasswordResetType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $password = $form->getData()["password"];
            $hashedPassword = $passwordHasher->hashPassword($user, $password);
            $user->setPassword($hashedPassword);
            $structure->setLastConnection(new \DateTime('now'));
            $entityManager = $manager->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            $entityManager = $manager->getManager();
            $entityManager->persist($structure);
            $entityManager->flush();
            $this->addFlash("success","Ton mot de passe à bien été modifié");
        }
        return $this->render("security/edit-password.html.twig", [
            'message' => "It's good structure",
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
