<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


/**
 * @Route("/structure_read")
 * @IsGranted("ROLE_STRUCTURE")
 */
class StructureController extends AbstractController
{   
    /**
     * @Route("/", name="read_structure")
     */
    #[Route("/structure", name: "app_structure")]
    public function index(): Response
    {
        return $this->render('structure/index.html.twig', [
            'controller_name' => 'StructureController',
        ]);
    }
}
