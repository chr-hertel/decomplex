<?php

declare(strict_types=1);

namespace App\Controller;

use App\ComplexityDiff\Calculator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/", name="complexity_diff_")
 */
class ComplexityDiffController extends AbstractController
{
    /**
     * @Route("", name="index", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('base.html.twig');
    }

    /**
     * @Route("calculate", name="calculate", methods={"POST"})
     */
    public function calculate(Request $request, Calculator $calculator): JsonResponse
    {
        /** @var string $code */
        $code = $request->getContent();

        return new JsonResponse($calculator->calculateComplexities($code));
    }
}
