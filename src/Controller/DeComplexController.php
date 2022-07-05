<?php

declare(strict_types=1);

namespace App\Controller;

use App\DeComplex\CodeDiffer;
use App\DeComplex\ComplexityCalculator;
use App\DeComplex\Exception\CalculationException;
use App\DeComplex\Exception\ParserException;
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
    public function index(Diff $diff = null, string $id = null): Response
    {
        return $this->render('index.html.twig', [
            'diff' => $diff,
            'missing_diff' => null === $diff && null !== $id,
        ]);
    }

    #[Route('calculate', name: 'calculate', methods: ['POST'], defaults: ['_format' => 'json'])]
    public function calculate(Request $request, ComplexityCalculator $calculator): JsonResponse
    {
        /** @var string $code */
        $code = $request->getContent();

        try {
            $snippet = $calculator->analyze($code);
        } catch (ParserException $exception) {
            throw new BadRequestHttpException($exception->getMessage(), $exception);
        } catch (CalculationException $exception) {
            return new JsonResponse($exception, 400);
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
