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
use Symfony\Component\Mailer\MailerInterface;
use App\Entity\Permit;
use App\Repository\PermitRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/admin")
 * @isGranted("ROLE_ADMIN") 
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/", name="app_admin")
     */
    public function index(FranchiseRepository $franchiseRepository, UserRepository $userRepository,Request $request, ManagerRegistry $manager)
    {      
        $franchises = $franchiseRepository->findAll();        
        $filter = $request->get("filter");

        $ajax = $request->query->get("ajax");
        $search = $request->query->get("search");
        $filter = $request->query->get("filter");
        $franchises = $franchiseRepository->findByFilters($filter, $search);

        // check is ajax request
        if($ajax){
   
            return new JsonResponse([
                "content" => $this->renderView('content/franchises.html.twig', [
                    "franchises" => $franchises,
                ])
            ]);
            
        }
    
        return $this->render('admin/index.html.twig', [
            "franchises" => $franchises,   
        ]);

    }
     

    
    /**
     * @Route("/edit_franchise/{id}", name="app_edit_franchise")
     */
    public function edit_franchise($id, MailerInterface $mailer,  StructureRepository $structureRepository,userRepository $userRepository, FranchiseRepository $franchiseRepository, ManagerRegistry $manager,PermitRepository $permitRepository,Request $request){

        $franchise = $franchiseRepository->FindOneBy(["id" => $id]);
        $user= $franchise->getUserInfo();
        $permit = $franchise->getPermit();
        $structures = $franchise->getStructures();
        $mail = false;

        $form = $this->createForm(IsActiveType::class, $franchise);
        
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $entityManager = $manager->getManager();
            $entityManager->persist($franchise);
            $entityManager->flush(); 
            $this->addFlash('success', 'Les droits par défaut de la franchise ont été enregisté.');
            
            //send mail to franchise 
            $email = (new TemplatedEmail())
            ->from('fitngo@outlook.fr')
            ->to($user->getEmail())
            ->subject("Permissions de votre franchise modifié !")
            ->text('Sending emails is fun again!')
            ->htmlTemplate('mail/global-permission.html.twig')
            ->context([
                'franchise' => $franchise,
                'permit' => $permit,
            ]);
            
            $mailer->send($email);
            $mail = true;
        }
        
        $ajax = $request->query->get("ajax");
        $search = $request->query->get("search");
        $filter = $request->query->get("filter");

        if($ajax){
            $structures = $structureRepository->findByFilters($filter, $search, $id);
            return new JsonResponse([
                "content" => $this->renderView('content/structures.html.twig', [
                    "franchise"=>$franchise,
                    "id" => $id,
                    "structures"=>$structures,
                    "form" => $form->createView()
                ])
            ]);
        }
        
        return $this->render(
            "admin/franchise.html.twig", 
            [
                "franchise"=>$franchise,
                "id" => $id,
                "structures"=>$structures,
                "form" => $form->createView(),
                "mail" => $mail
            ]
        );
    }

    /**
     * @Route("/edit_structure/{id}", name="app_edit_structure")
     */
    public function edit_structure($id, MailerInterface $mailer, StructureRepository $structureRepository, IsActiveType $isActiveType , ManagerRegistry $manager,PermitRepository $permitRepository,Request $request){

        $structure = $structureRepository->FindOneBy(["id" => $id]);
        $franchise = $structure->getFranchise();
        $mail= false;
 
        $permit = $permitRepository->findOneBy(["id" => $structure->getPermit()->getId()]);

        $form = $this->createForm(PermitType::class, $permit);
        $form->handleRequest($request);

        $form = $this->createForm(IsActiveType::class, $structure);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $entityManager = $manager->getManager();
            $entityManager->persist($structure);
            $entityManager->flush(); 
            $this->addFlash('success', 'Les modifications des droits ont été enregistés.');
            
            
            $email = (new TemplatedEmail())
            ->from('fitngo@outlook.fr')
            ->to($structure->getUserInfo()->getEmail())
            ->subject("Les accès de votre structure ont été modifiés")
            ->text('Sending emails is fun again!')
            ->htmlTemplate('mail/permission.html.twig')
            ->context([
                'structure' => $structure,
                'permit' => $structure->getPermit()
            ]);
            $mailer->send($email);


            $emailtoFranchise = (new TemplatedEmail())
            ->from('fitngo@outlook.fr')
            ->to($franchise->getUserInfo()->getEmail())
            ->subject("Les accès d'une de vos structure ont été modifiés")
            ->text('Sending emails is fun again!')
            ->htmlTemplate('mail/permission.html.twig')
            ->context([
                'structure' => $structure,
                'permit' => $structure->getPermit(),
                'franchise' => $franchise
            ]);
            $mailer->send($emailtoFranchise);
            
            $mail = true;
        }

        return $this->render("admin/structures.html.twig", [
            "structure" => $structure,
            "franchise" => $franchise,
            "id" => $id,
            "form" => $form->createView(),
            "mail" => $mail
        ]);
    }


    /**
     * @Route("/create_franchise", name="app_create_franchise")
     */
    public function create_franchise(Request $request, MailerInterface $mailer ,ManagerRegistry $manager, UserPasswordHasherInterface $passwordHasher, ValidatorInterface $validator, UserRepository $userRepository )
    {

        $user = new User();
        $form = $this->createForm(NewFranchiseType::class, $user);
        $form->handleRequest($request);

        /** if form is correct */
        if($form->isSubmitted() && $form->isValid()){
                
                /* fetch posted datas */
                $mail = $form->getData()->getEmail();
                $name = $form->getData()->getName();

                /* hydrate my entities */
                $permit = new Permit();
                $franchise = new Franchise();
                $permit->setOptions(false);
                $franchise->setPermit($permit);
                $franchise->setIsActive(false);
                $franchise->setUserInfo($user);
                $user->setEmail($mail);
                $user->setName($name);
                $user->setRoles(["ROLE_FRANCHISE"]);
                $user->setFranchise($franchise);
                /** Generate token password */
                $bytes = openssl_random_pseudo_bytes(8);
                $password = bin2hex($bytes);

                // generate token url connexion
                $bytes = openssl_random_pseudo_bytes(6);
                $token = bin2hex($bytes);
                $user->setUrl($token);

                // $password = "admin";
                $hashedPassword = $passwordHasher->hashPassword($user, $password);
                $user->setPassword($hashedPassword);


                /* flushing datas in db */
                $entityManager = $manager->getManager();
                $entityManager->persist($user);
                $entityManager->flush();              
                $this->addFlash('success', 'La franchise '.$name.' a bien été enregistré.');

                $email = (new TemplatedEmail())
                ->from('fitngo@outlook.fr')
                ->to($mail)
                ->subject("Franchise créée")
                ->text('Sending emails is fun again!')
                ->htmlTemplate('mail/new_franchise.html.twig')
                ->context([
                    'name' => $name,
                    'mail' => $mail,
                    'password' => $password,
                ]);
       
                $mailer->send($email);
                return $this->render("security/creation-franchise.html.twig", [
                    "form" => $form->createView(),
                    "id" => $user->getId()
                ]);    
            }
        return $this->render("security/creation-franchise.html.twig", ["form" => $form->createView()]);   
    }



    /**
     * @Route("/create_structure/{id}", name="create_structure")
     */
    public function create_structure($id, Request $request,  MailerInterface $mailer,UserRepository $userRepository, franchiseRepository $franchiseRepository,ManagerRegistry $manager, UserPasswordHasherInterface $passwordHasher, PermitRepository $permitRepository){
       
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
            $mail = $form->getData()->getEmail();
            
            $user = new User();
            $user->setName($name);
            $user->setEmail($mail);
            $user->setRoles(["ROLE_STRUCTURE"]);
            $user->setStructure($structure);

            /** Generate token password */
            $bytes = openssl_random_pseudo_bytes(8);
            $password = bin2hex($bytes);
            $hashedPassword = $passwordHasher->hashPassword($user, $password);
            $user->setPassword($hashedPassword);

            $bytes = openssl_random_pseudo_bytes(6);
            $token = bin2hex($bytes);
            $user->setUrl($token);

            $entityManager = $manager->getManager();
            $entityManager->persist($user);
            $entityManager->flush();   
            $this->addFlash("success", "La structure ".$name." a été créée");     


            // sending Mail to Structure and Franchise
            $email = (new TemplatedEmail())
            ->from('fitngo@outlook.fr')
            ->to($mail)
            ->subject("Structrue créée")
            ->text('Sending emails is fun again!')
            ->htmlTemplate('mail/new_structure.html.twig')
            ->context([
                'name' => $name,
                'mail' => $mail,
                'password' => $password,
            ]);
   
            $mailer->send($email);

            $userFranchise = $userRepository->findOneBy(["Franchise" => $franchise]);
            $emailFranchise = (new TemplatedEmail())
            ->from('fitngo@outlook.fr')
            ->to($userFranchise->getEmail())
            ->subject("Une nouvelle structrue créée !")
            ->text('Sending emails is fun again!')
            ->htmlTemplate('mail/new_structure.html.twig')
            ->context([
                'franchise_name' => $userFranchise->getName(),
                'name' => $name,
            ]);
   
            $mailer->send($emailFranchise);

            return $this->render("security/creation-structure.html.twig", [
                "form" => $form->createView(),
                "franchise" => $franchise,
                "new_structure_id" => $user->getId(), 
            ]); 
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