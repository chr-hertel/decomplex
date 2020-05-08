<?php

declare(strict_types=1);

namespace App\ComplexityDiff;

final class Calculation implements \JsonSerializable
{
    private int $cyclomaticComplexity;
    private int $cognitiveComplexity;

    public function __construct(int $cyclomaticComplexity, int $cognitiveComplexity)
    {
        $this->cyclomaticComplexity = $cyclomaticComplexity;
        $this->cognitiveComplexity = $cognitiveComplexity;
    }

    public function jsonSerialize(): array
    {
        return [
            'cyclomatic_complexity' => $this->cyclomaticComplexity,
            'cognitive_complexity' => $this->cognitiveComplexity,
        ];
    }
}
