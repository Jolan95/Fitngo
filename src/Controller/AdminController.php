<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface; 
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;
use App\Entity\Franchise;
use App\Entity\Permit;


class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
    #[Route(path: '/signin', name: 'app_signin')]
    public function signin(ManagerRegistry $manager, UserPasswordHasherInterface $passwordHasher)
    {
        $user = new User();
        $franchise = new Franchise();
        $permit = new Permit();

        $permit->setDetailedData(false);
        $permit->setLiveChat(false);
        $permit->setVirtualTraining(false);
        $permit->setNewsletter(false);
        $permit->setTeamSchedule(false);
        $permit->setPaymentOnline(false);

        $franchise->setPermit($permit);
        $franchise->setIsActive(false);
        $franchise->setUserInfo($user);
        
        
        $password = "admin";
        $hashedPassword = $passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);
        $user->setEmail("bordeaux@fitngo.fr");
        $user->setRoles(["ROLE_FRANCHISE"]);
        $user->setFranchise($franchise);
        $entityManager = $manager->getManager();
        $entityManager->persist($user);
        $entityManager->flush();
        
        
        
        return new Response("maybe well pushed");
    }
}
