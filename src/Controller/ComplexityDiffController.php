<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ComplexityDiffController extends AbstractController
{
    /**
     * @Route("/", name="complexity_diff", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('base.html.twig');
    }
}
