<?php

declare(strict_types=1);

namespace App\ComplexityDiff;

use LogicException;
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
            throw new LogicException('Unable to parse given source code');
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
