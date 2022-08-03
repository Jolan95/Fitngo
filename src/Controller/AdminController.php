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
use App\Form\IsActiveType;
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
    public function index(FranchiseRepository $franchiseRepository, Request $request, ManagerRegistry $manager): Response
    {      
        $franchises = $franchiseRepository->findAll();

        return $this->render('admin/index.html.twig', [
            "franchises" => $franchises
        ]);
    }
     

    
    /**
     * @Route("/edit_franchise/{id}", name="app_edit_franchise")
     */
    public function edit_franchise($id, userRepository $userRepository, FranchiseRepository $franchiseRepository, ManagerRegistry $manager,PermitRepository $permitRepository,Request $request){

        $franchise= $franchiseRepository->FindOneBy(["id" => $id]);
        $structures = $franchise->getStructures();

        $permit = $permitRepository->findOneBy(["id" => $franchise->getPermit()->getId()]);

        $form = $this->createForm(IsActiveType::class, $franchise);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $entityManager = $manager->getManager();
            $entityManager->persist($franchise);
            $entityManager->flush();   
        }


        return $this->render(
            "admin/franchise.html.twig", 
            [
                "franchise"=>$franchise,
                "id" => $id,
                "structures"=>$structures,
                "form" => $form->createView()
            ]
        );
    }

    /**
     * @Route("/edit_structure/{id}", name="app_edit_structure")
     */
    public function edit_structure($id, StructureRepository $structureRepository, IsActiveType $isActiveType , ManagerRegistry $manager,PermitRepository $permitRepository,Request $request){

        $structure = $structureRepository->FindOneBy(["id" => $id]);
        $franchise = $structure->getFranchise();
        

        // $franchise = $franchiseRepository->findOneBy(["id" => $structure=>g])
 
        $permit = $permitRepository->findOneBy(["id" => $structure->getPermit()->getId()]);

        $form = $this->createForm(PermitType::class, $permit);
        $form->handleRequest($request);

        $form = $this->createForm(IsActiveType::class, $structure);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $entityManager = $manager->getManager();
            $entityManager->persist($structure);
            $entityManager->flush();   
        }



        return $this->render("admin/structures.html.twig", ["structure" => $structure,"franchise" => $franchise ,"id" => $id, "form" => $form->createView()]);
    }


    /**
     * @Route("/create_franchise", name="app_create_franchise")
     */
    public function create_franchise(Request $request, ManagerRegistry $manager, UserPasswordHasherInterface $passwordHasher, ValidatorInterface $validator, UserRepository $userRepository )
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
                $bytes = openssl_random_pseudo_bytes(6);
                $token = bin2hex($bytes);
                $user->setUrl($token);
                $password = "admin";
                $hashedPassword = $passwordHasher->hashPassword($user, $password);
                $user->setPassword($hashedPassword);

                /* flushing datas in db */
                $entityManager = $manager->getManager();
                $entityManager->persist($user);
                $entityManager->flush();              
                $this->addFlash('success', 'La franchise '.$name.' a bien été enregistré.');
                return $this->redirect($request->getUri());  
                     
            }
        return $this->render("security/creation-franchise.html.twig", ["form" => $form->createView()]);   
    }



    /**
     * @Route("/create_structure/{id}", name="create_structure")
     */
    public function create_structure($id, Request $request, franchiseRepository $franchiseRepository,ManagerRegistry $manager, UserPasswordHasherInterface $passwordHasher, PermitRepository $permitRepository){
       
        $franchise = $franchiseRepository->findOneBy(['id' => $id]);
        
        $form = $this->createForm(NewStructureType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            
            $permit = new Permit();   
            $permit->setPaymentOnline($franchise->getPermit()->isPaymentOnline());
            $permit->setNewsletter($franchise->getPermit()->isNewsletter());
            $permit->setTeamSchedule($franchise->getPermit()->isTeamSchedule());
            $permit->setLiveChat($franchise->getPermit()->isLiveChat());
            $permit->setVirtualTraining($franchise->getPermit()->isVirtualTraining());
            $permit->setDetailedData($franchise->getPermit()->isDetailedData());
            
            $structure  = new Structure();
            $structure->setPermit($permit);
            $structure->setFranchise($franchise);
            $structure->setIsActive(false);

            /* fetch form datas */
            $name = $form->getData()->getName();
            $email = $form->getData()->getEmail();
            
            $user = new User();
            $user->setName($name);
            $user->setEmail($email);
            $user->setRoles(["ROLE_STRUCTURE"]);
            $user->setStructure($structure);
            $password = "admin";
            $hashedPassword = $passwordHasher->hashPassword($user, $password);
            $user->setPassword($hashedPassword);

            $bytes = openssl_random_pseudo_bytes(6);
            $token = bin2hex($bytes);
            $user->setUrl($token);

            $entityManager = $manager->getManager();
            $entityManager->persist($user);
            $entityManager->flush();   
            $this->addFlash("success", "La structure ".$name." a été créée");     

        }
        
        return $this->render("security/creation-structure.html.twig", ["form" => $form->createView(), "franchise" => $franchise]);
    }


    /**
     * @Route("/remove_franchise/{id}", name="remove_franchise")
     */
    public function remove_franchise($id, Request $request, UserRepository $userRepository,  franchiseRepository $franchiseRepository,ManagerRegistry $manager, UserPasswordHasherInterface $passwordHasher, PermitRepository $permitRepository){
       
        $user = $userRepository->findOneBy(['id' => $id]);
        
        $entityManager = $manager->getManager();
        $entityManager->remove($user);
        $entityManager->flush();   
        $this->addFlash('success', 'La franchise '.$user->getName().' a bien été supprimé.');

        
        return $this->redirectToRoute('app_admin');
    }

    /**
     * @Route("/remove_structure/{id}", name="remove_structure")
     */
    public function remove_structure($id, Request $request,StructureRepository $structureRepository, UserRepository $userRepository,  franchiseRepository $franchiseRepository,ManagerRegistry $manager, UserPasswordHasherInterface $passwordHasher, PermitRepository $permitRepository){
       
        $structure = $structureRepository->findOneBy(["id" => $id]);
        $franchise = $structure->getFranchise()->getId();
        $user = $structure->getUserInfo();
        
        $entityManager = $manager->getManager();
        $entityManager->remove($user);
        $entityManager->flush();
        $this->addFlash('success', 'La structure '.$user->getName().' a bien été supprimé.');
           
 
        
        return $this->redirectToRoute('app_edit_franchise', ["id" => $franchise]);
    }
}
