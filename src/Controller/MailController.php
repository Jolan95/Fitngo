<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\FranchiseRepository;
use App\Repository\StructureRepository;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


/**
 * @Route("/mail")
 * @isGranted("ROLE_ADMIN") 
 */
class MailController extends AbstractController
{
    /**
     * @Route("/new-franchise/{id}", name="email_new_franchise")
     */
    public function new_franchise($id, FranchiseRepository $franchiseRepository, UserRepository $userRepository )
    {
        
        $user = $userRepository->FindOneBy(["id" => $id]);
        return $this->render("mail/new_franchise.html.twig", [
            "name" => $user->getName(),
            "mail" => $user->getEmail(),
            "password" => "dpfero"
        ]);
    }
    /**
     * @Route("/new-structure/{user_id}", name="email_new_structure")
     */
    public function new_structure($user_id, FranchiseRepository $franchiseRepository, UserRepository $userRepository )
    {
        
        $user = $userRepository->FindOneBy(["id" => $user_id]);
        return $this->render("mail/new_structure.html.twig", [
            "name" => $user->getName(),
            "mail" => $user->getEmail(),
            "password" => "****** (mot de passe seulement dans le véritable mail)"
        ]);
    }
    /**
     * @Route("/new-structure/{user_id}/{franchise_id}", name="email_new_structure_toFranchise")
     */
    public function new_structure_toFranchise($franchise_id, $user_id, FranchiseRepository $franchiseRepository, UserRepository $userRepository, StructureRepository $structureRepository )
    {
        
        $user = $userRepository->FindOneBy(["id" => $user_id]);
        $franchise = $franchiseRepository->findOneBy(["id" => $franchise_id]);
        return $this->render("mail/new_structure.html.twig", [
            'franchise_name' => $franchise->getUserInfo()->getName(),
            'name' => $user->getName(),
        ]);
    }



}
