<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Repository\StructureRepository;
use App\Repository\FranchiseRepository;
use App\Repository\PermitRepository;
use App\Repository\UserRepository;
use Exception;

class FranchiseController extends AbstractController
{
    /**
     * @Route("/franchise/{token}", name="read_franchise")
     */
    public function franchise($token, UserRepository $userRepository): Response
    {
        if( $this->getUser()->getUrl() === $token){

            $user = $userRepository->findOneBy(["url" => $token]);
            $franchise = $user->getFranchise();   
            $permit = $franchise->getPermit();
            
            return $this->render('read-only/franchise.html.twig', [
                'franchise' => $franchise,
                "permit" => $permit
            ]);
        } else{
            // error if the franchise doesn't belong to the user
            throw new Exception("Vous n'avez pas accès à cette page", 500);
        }
    }
    /**
     * 
     * @Route("/structure/{token}", name="read_structure")
     */
    public function structure($token, PermitRepository $permitRepository, userRepository $userRepository, StructureRepository $structureRepository, FranchiseRepository $franchiseRepository): Response
    {
        $user = $this->getUser();
        $role = $user->getRoles();
        // url of the user connected
        $url = $user->getUrl();
        // entity user corresponding to url token
        $userToken = $userRepository->findOneBy(["url" => $token]);
        // structure of the token
        $structure = $structureRepository->findOneBy(["id" => $userToken->getStructure()->getId()]);
        $permit = $structure->getPermit();
        
        
        
        
        
        // find the user role
        if (in_array("ROLE_STRUCTURE", $role)){
            // if structure is the user's structure
            if($user->getUrl() === $token){   
                return $this->render('read-only/structure.html.twig', [
                    'structure' => $user->getStructure(),
                    'permit' => $permit
                ]); 
            }
            throw new Exception ("Cette page ne correspond pas à votre structure", 500);

        } else if(in_array("ROLE_FRANCHISE", $role)){
                //  if the structure belongs to the user's franchise
                if($structure->getFranchise()->getId() === $user->getFranchise()->getId());
                return $this->render('read-only/structure.html.twig', [
                    'structure' => $structure,
                    "url" => $url,
                    "permit" => $permit
                ]);
                //error if structure doesn't belong to the franchise user
                throw new Exception ("Cette structure ne correspond pas à votre franchise", 500);
            // if user is not franchise or structure
        }
        // if the user is not a structure or  a franchise
        throw new Exception("Cette page est réservé aux franchises et structure", 500);
        
    }
    
}
