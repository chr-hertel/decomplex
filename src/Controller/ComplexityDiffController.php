<?php

declare(strict_types=1);

namespace App\Controller;

use App\ComplexityDiff\Calculator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/", name="complexity_diff_")
 */
class ComplexityDiffController extends AbstractController
{
    /**
     * @Route("", name="index", methods={"GET"})
     * @codeCoverageIgnore
     */
    public function index(Calculator $calculator): Response
    {
        $codeLeft = (string) file_get_contents(__DIR__.'/../../tests/fixtures/camelcase-messy.php');
        $complexitiesLeft = $calculator->calculateComplexities($codeLeft);
        $codeSampleLeft = array_merge(['code' => $codeLeft], $complexitiesLeft->jsonSerialize());

        $codeRight = (string) file_get_contents(__DIR__.'/../../tests/fixtures/camelcase-clean.php');
        $complexitiesRight = $calculator->calculateComplexities($codeRight);
        $codeSampleRight = array_merge(['code' => $codeRight], $complexitiesRight->jsonSerialize());

        return $this->render('index.html.twig', [
            'code_sample_left' => $codeSampleLeft,
            'code_sample_right' => $codeSampleRight,
        ]);
    }

    /**
     * @Route("calculate", name="calculate", methods={"POST"}, defaults={"_format": "json"})
     */
    public function calculate(Request $request, Calculator $calculator): JsonResponse
    {
        /** @var string $code */
        $code = $request->getContent();

        try {
            $calculation = $calculator->calculateComplexities($code);
        } catch (\LogicException $exception) {
            throw new BadRequestHttpException($exception->getMessage(), $exception);
        }

        return new JsonResponse($calculation);
    }
}
