<?php

declare(strict_types=1);

namespace App\DeComplex;

use App\DeComplex\Exception\CalculationException;
use App\DeComplex\Exception\ParserException;
use App\Entity\Snippet;
use App\Repository\SnippetRepository;
use NdB\PhpDocCheck\Metrics\CognitiveComplexity;
use NdB\PhpDocCheck\Metrics\CyclomaticComplexity;
use PhpParser\ErrorHandler\Collecting;
use PhpParser\Node\Stmt;
use PhpParser\Parser;

final class ComplexityCalculator
{
    public function __construct(
        private readonly CodeHasher $codeHasher,
        private readonly SnippetRepository $snippetRepository,
        private readonly Parser $parser,
        private readonly CyclomaticComplexity $cyclomaticComplexity,
        private readonly CognitiveComplexity $cognitiveComplexity,
    ) {
    }

    public function analyze(string $code): Snippet
    {
        $hash = $this->codeHasher->hash($code);
        $snippet = $this->snippetRepository->findOneByHash($hash);

        if (null !== $snippet) {
            return $snippet;
        }

        $errorCollection = new Collecting();
        $ast = $this->parser->parse($code, $errorCollection);

        if (null === $ast) {
            throw new ParserException();
        }

        if ($errorCollection->hasErrors()) {
            throw new CalculationException($errorCollection);
        }

        return new Snippet(
            $code,
            $hash,
            $this->calculateCyclomaticComplexity($ast),
            $this->calculateCognitiveComplexity($ast)
        );
    }

    /**
     * @param Stmt[] $ast
     */
    private function calculateCyclomaticComplexity(array $ast): int
    {
        $calc = fn (int $complexity, Stmt $node): int => $complexity + $this->cyclomaticComplexity->getValue($node);

        return array_reduce($ast, $calc, 0);
    }

    /**
     * @param Stmt[] $ast
     */
    private function calculateCognitiveComplexity(array $ast): int
    {
        $calc = fn (int $complexity, Stmt $node): int => $complexity + $this->cognitiveComplexity->getValue($node);

        return array_reduce($ast, $calc, 0);
    }
}
