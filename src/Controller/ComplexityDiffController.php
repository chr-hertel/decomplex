<?php

declare(strict_types=1);

namespace App\Controller;

use App\ComplexityDiff\Calculator;
use App\ComplexityDiff\Persister;
use App\Entity\Diff;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @Route("/", name="complexity_diff_")
 */
class ComplexityDiffController extends AbstractController
{
    /**
     * @Route("", name="index", methods={"GET"})
     * @Route("/{id<[0-9a-zA-Z\-\_]{6}>}", name="permalink", methods={"GET"})
     */
    public function index(Diff $diff = null): Response
    {
        return $this->render('index.html.twig', [
            'diff' => $diff,
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
            $snippet = $calculator->calculateComplexities($code);
        } catch (\LogicException $exception) {
            throw new BadRequestHttpException($exception->getMessage(), $exception);
        }

        return new JsonResponse($snippet);
    }

    /**
     * @Route("permalink", name="create_permalink", methods={"POST"}, defaults={"_format": "json"})
     */
    public function permalink(Request $request, Persister $persister): JsonResponse
    {
        $diff = $persister->persistDiff(
            $request->request->get('left', ''),
            $request->request->get('right', ''),
        );

        return new JsonResponse(
            $this->generateUrl('complexity_diff_permalink', ['id' => $diff->getId()], UrlGeneratorInterface::ABSOLUTE_URL)
        );
    }
}
