<?php

declare(strict_types=1);

namespace App\Controller;

use App\ComplexityDiff\Calculator;
use App\Entity\Diff;
use Doctrine\ORM\EntityManagerInterface;
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
    public function index(Calculator $calculator, Diff $diff = null): Response
    {
        $diff ??= new Diff();

        return $this->render('index.html.twig', [
            'left' => [
                'code' => $diff->getCodeSnippetLeft(),
                'complexity' => $calculator->calculateComplexities($diff->getCodeSnippetLeft())->jsonSerialize(),
            ],
            'right' => [
                'code' => $diff->getCodeSnippetRight(),
                'complexity' => $calculator->calculateComplexities($diff->getCodeSnippetRight())->jsonSerialize(),
            ],
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

    /**
     * @Route("permalink", name="create_permalink", methods={"POST"})
     */
    public function permalink(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $diff = new Diff(
            $request->request->get('left', ''),
            $request->request->get('right', '')
        );

        $entityManager->persist($diff);
        $entityManager->flush();

        return new JsonResponse(
            $this->generateUrl('complexity_diff_permalink', ['id' => $diff->getId()], UrlGeneratorInterface::ABSOLUTE_URL)
        );
    }
}
