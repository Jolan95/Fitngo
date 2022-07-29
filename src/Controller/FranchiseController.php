<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;




/**
 * @Route("/franchise_read")
 * @IsGranted("ROLE_FRANCHISE")
 */
class FranchiseController extends AbstractController
{
    /**
     * @Route("/", name="read_franchise")
     */
    public function index(): Response
    {
        return $this->render('franchise/index.html.twig', [
            'controller_name' => 'FranchiseController',
        ]);
    }
}
