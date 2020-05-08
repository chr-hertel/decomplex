<?php

declare(strict_types=1);

namespace App\ComplexityDiff;

use NdB\PhpDocCheck\Metrics\CognitiveComplexity;
use NdB\PhpDocCheck\Metrics\CyclomaticComplexity;
use PhpParser\ErrorHandler\Collecting;
use PhpParser\Node\Stmt;
use PhpParser\Parser;

final class Calculator
{
    private Parser $parser;
    private CyclomaticComplexity $cyclomaticComplexity;
    private CognitiveComplexity $cognitiveComplexity;

    public function __construct(
        Parser $parser,
        CyclomaticComplexity $cyclomaticComplexity,
        CognitiveComplexity $cognitiveComplexity
    ) {
        $this->parser = $parser;
        $this->cyclomaticComplexity = $cyclomaticComplexity;
        $this->cognitiveComplexity = $cognitiveComplexity;
    }

    public function calculateComplexities(string $code): Calculation
    {
        $errorCollection = new Collecting();
        $ast = $this->parser->parse($code, $errorCollection);

        if (null === $ast || $errorCollection->hasErrors()) {
            throw new \LogicException('Unable to parse given source code');
        }

        return new Calculation(
            $this->calculateCyclomaticComplexity($ast),
            $this->calculateCognitiveComplexity($ast)
        );
    }

    /**
     * @param Stmt[] $ast
     */
    private function calculateCyclomaticComplexity(array $ast): int
    {
        return array_reduce($ast, function (int $complexity, Stmt $node) {
            return $complexity + $this->cyclomaticComplexity->getValue($node);
        }, 0);
    }

    /**
     * @param Stmt[] $ast
     */
    private function calculateCognitiveComplexity(array $ast): int
    {
        return array_reduce($ast, function (int $complexity, Stmt $node) {
            return $complexity + $this->cognitiveComplexity->getValue($node);
        }, 0);
    }
}
