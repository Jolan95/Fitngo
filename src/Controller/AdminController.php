<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface; 
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Form\NewFranchiseType;
use App\Form\NewStructureType;
use App\Form\PermitType;
use App\Repository\UserRepository;
use App\Repository\FranchiseRepository;
use App\Repository\StructureRepository;
use App\Entity\User;
use App\Entity\Structure;
use App\Entity\Franchise;
use App\Entity\Permit;
use App\Repository\PermitRepository;

/**
 * @Route("/admin")
 * @isGranted("ROLE_ADMIN") 
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/", name="app_admin")
     */
    public function index(FranchiseRepository $franchiseRepository): Response
    {      
        $franchises = $franchiseRepository->findAll();


        return $this->render('admin/index.html.twig', [
            "franchises" => $franchises
        ]);
    }
      

    
    /**
     * @Route("/edit_franchise/{id}", name="app_edit_franchise")
     */
    public function edit_franchise($id, FranchiseRepository $franchiseRepository, ManagerRegistry $manager,PermitRepository $permitRepository,Request $request){

        $franchise = $franchiseRepository->FindOneBy(["id" => $id]);
        $permit = $permitRepository->findOneBy(["id" => $franchise->getPermit()->getId()]);

        $form = $this->createForm(PermitType::class, $permit);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            $entityManager = $manager->getManager();
            $entityManager->persist($franchise);
            $entityManager->flush();   
        }

        return $this->render("admin/franchise.html.twig", ["franchise"=>$franchise,"id" => $id, "form" => $form->createView()]);
    }



    /**
     * @Route("/edit_structure/{id}", name="app_edit_structure")
     */
    public function edit_structure($id, StructureRepository $structureRepository, FranchiseRepository $franchiseRepository, ManagerRegistry $manager,PermitRepository $permitRepository,Request $request){

        $structure = $structureRepository->FindOneBy(["id" => $id]);
        $permit = $permitRepository->findOneBy(["id" => $structure->getPermit()->getId()]);

        $form = $this->createForm(PermitType::class, $permit);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            $entityManager = $manager->getManager();
            $entityManager->persist($structure);
            $entityManager->flush();   
        }

        return $this->render("admin/structure.html.twig", ["id" => $id, "form" => $form->createView()]);
    }


    /**
     * @Route("/create_franchise", name="app_create_franchise")
     */
    public function dreate_franchise(Request $request, ManagerRegistry $manager, UserPasswordHasherInterface $passwordHasher, ValidatorInterface $validator, UserRepository $userRepository )
    {

        $user = new User();
        $form = $this->createForm(NewFranchiseType::class, $user);
        $form->handleRequest($request);

        /** if form is correct */
        if($form->isSubmitted() && $form->isValid()){
                
                /* fetch posted datas */
                $email = $form->getData()->getEmail();
                $name = $form->getData()->getName();

                /* hydrate my entities */
                $permit = new Permit();
                $franchise = new Franchise();
                $permit->setOptions(false);
                $franchise->setPermit($permit);
                $franchise->setIsActive(false);
                $franchise->setUserInfo($user);
                $user->setEmail($email);
                $user->setName($name);
                $user->setRoles(["ROLE_FRANCHISE"]);
                $user->setFranchise($franchise);
                /** Generate token password */
                // $bytes = openssl_random_pseudo_bytes(8);
                // $password = bin2hex($bytes);
                $password = "admin";
                $hashedPassword = $passwordHasher->hashPassword($user, $password);
                $user->setPassword($hashedPassword);

                /* flushing datas in db */
                $entityManager = $manager->getManager();
                $entityManager->persist($user);
                $entityManager->flush();              
                $this->addFlash('success', 'La nouvelle entité a bien été enregistré.');
                return $this->redirect($request->getUri());  
                     
            }
        return $this->render("security/form.html.twig", ["form" => $form->createView()]);   
    }

    /**
     * @Route("/create_structure/{id}", name="create_structure")
     */
    public function create_structure($id, Request $request, franchiseRepository $franchiseRepository,ManagerRegistry $manager, UserPasswordHasherInterface $passwordHasher, UserRepository $userRepository){
        $franchise = $franchiseRepository->findOneBy(['id' => $id]);

        $form = $this->createForm(NewStructureType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $structure  = new Structure();
            $user = new User;
            $permit = new Permit;   
            $permit->setOptions(false);

            $structure->setPermit($permit);
            $structure->setFranchise($franchise);
            $structure->setIsActive(false);

            $name = $form->getData()->getName();
            $email = $form->getData()->getEmail();

            $user->setName($name);
            $user->setEmail($email);
            $user->setRoles(["ROLE_STRUCTURE"]);
            $user->setStructure($structure);
            $password = "admin";
            $hashedPassword = $passwordHasher->hashPassword($user, $password);
            $user->setPassword($hashedPassword);

            $entityManager = $manager->getManager();
            $entityManager->persist($user);
            $entityManager->flush();   
            $this->addFlash("success", "Structure créée");     

        }
        
        return $this->render("security/form.html.twig", ["form" => $form->createView()]);
    }
}
