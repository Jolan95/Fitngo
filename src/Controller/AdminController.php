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
use App\Repository\UserRepository;
use App\Entity\User;
use App\Entity\Franchise;
use App\Entity\Permit;

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
     * @Route("/create_structure", name="create_structure")
     */
    public function create_structure(Request $request, ManagerRegistry $manager, UserPasswordHasherInterface $passwordHasher, UserRepository $userRepository){
        
        $form = $this->createForm(NewFranchiseType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // data is an array with "name", "email", and "message" keys
            $data = $form->getData();
            dd($data);
        }
        
        return $this->render("security/form.html.twig", ["form" => $form->createView()]);
    }
}
