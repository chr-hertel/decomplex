<?php

declare(strict_types=1);

namespace App\Controller;

use App\DeComplex\CodeDiffer;
use App\DeComplex\ComplexityCalculator;
use App\Entity\Diff;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/', name: 'decomplex_')]
final class DeComplexController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    #[Route('/{id<[0-9a-zA-Z\-\_]{6}>}', name: 'permalink', methods: ['GET'])]
    public function index(Diff $diff = null): Response
    {
        return $this->render('index.html.twig', [
            'diff' => $diff,
        ]);
    }

    #[Route('calculate', name: 'calculate', methods: ['POST'], defaults: ['_format' => 'json'])]
    public function calculate(Request $request, ComplexityCalculator $calculator): JsonResponse
    {
        /** @var string $code */
        $code = $request->getContent();

        try {
            $snippet = $calculator->analyze($code);
        } catch (\LogicException $exception) {
            throw new BadRequestHttpException($exception->getMessage(), $exception);
        }

        return new JsonResponse($snippet);
    }

    #[Route('permalink', name: 'create_permalink', methods: ['POST'], defaults: ['_format' => 'json'])]
    public function permalink(Request $request, CodeDiffer $differ): JsonResponse
    {
        $leftCode = (string) $request->request->get('left', '');
        $rightCode = (string) $request->request->get('right', '');

        $diff = $differ->create($leftCode, $rightCode);

        return new JsonResponse(
            $this->generateUrl('decomplex_permalink', ['id' => $diff->getId()], UrlGeneratorInterface::ABSOLUTE_URL)
        );
    }
}
