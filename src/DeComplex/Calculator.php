<?php

declare(strict_types=1);

namespace App\DeComplex;

use App\Entity\Snippet;
use App\Repository\SnippetRepository;
use LogicException;
use NdB\PhpDocCheck\Metrics\CognitiveComplexity;
use NdB\PhpDocCheck\Metrics\CyclomaticComplexity;
use PhpParser\ErrorHandler\Collecting;
use PhpParser\Node\Stmt;
use PhpParser\Parser;

final class Calculator
{
    public function __construct(
        private readonly SnippetRepository $snippetRepository,
        private readonly Parser $parser,
        private readonly CyclomaticComplexity $cyclomaticComplexity,
        private readonly CognitiveComplexity $cognitiveComplexity,
    ) {
    }

    public function calculateComplexities(string $code): Snippet
    {
        $snippet = $this->snippetRepository->findOneByHash(Snippet::hash($code));

        if (null !== $snippet) {
            return $snippet;
        }

        $errorCollection = new Collecting();
        $ast = $this->parser->parse($code, $errorCollection);

        if (null === $ast || $errorCollection->hasErrors()) {
            throw new LogicException('Unable to parse given source code');
        }

        return new Snippet(
            $code,
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
