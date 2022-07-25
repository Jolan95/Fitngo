<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface; 
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Form\NewFranchiseType;
use App\Form\NewStructureType;
use App\Repository\UserRepository;
use App\Entity\User;
use App\Entity\Franchise;
use App\Entity\Permit;
use Exception;

/**
 * @Route("/admin")
 * @isGranted("ROLE_ADMIN") 
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/", name="app_admin")
     */
    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {   

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("/create_franchise", name="app_create_structure")
     */
    public function dreate_franchise(ManagerRegistry $manager, UserPasswordHasherInterface $passwordHasher, UserRepository $userRepository )
    {
        $form = $this->createForm(NewFranchiseType::class);

        if($form->isSubmitted()){
            dd($form->getData());
        }
        // $email= 'bordeau@fitngo.fr';
        // $isEmailExisting = $userRepository->findByEmail($email);

        // if(!$isEmailExisting){

        //     $user = new User();
        //     $franchise = new Franchise();
        //     $permit = new Permit();
        //     $permit->setDetailedData(false);
        //     $permit->setLiveChat(false);
        //     $permit->setVirtualTraining(false);
        //     $permit->setNewsletter(false);
        //     $permit->setTeamSchedule(false);
        //     $permit->setPaymentOnline(false);
        //     $franchise->setPermit($permit);
        //     $franchise->setIsActive(false);
        //     $franchise->setUserInfo($user);
        //     $password = "admin";
        //     $hashedPassword = $passwordHasher->hashPassword($user, $password);
        //     $user->setPassword($hashedPassword);
        //     $user->setEmail($email);
        //     $user->setRoles(["ROLE_FRANCHISE"]);
        //     $user->setFranchise($franchise);
        //     $entityManager = $manager->getManager();
        //     $entityManager->persist($user);
        //     $entityManager->flush();
        //     return new Response("maybe well pushed");
        // } else{
        //     return new Response("email already existing!");
        // }
        return $this->render("security/form.html.twig", ["form" => $form->createView()]);
        
        
    }

    /**
     * @Route("/create_structure", name="create_structure")
     */
    public function create_structure(ManagerRegistry $manager, UserPasswordHasherInterface $passwordHasher, UserRepository $userRepository){
        
        $form =$this->createForm(NewStructureType::class);

        if($form->isSubmitted()){
            return new Response("did");
        }
        return $this->render("security/form.html.twig", ["form" => $form->createView()]);
    }
}
