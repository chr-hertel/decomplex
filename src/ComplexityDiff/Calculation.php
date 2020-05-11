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

    /**
     * @return array<string, array|string>
     */
    public function jsonSerialize(): array
    {
        return [
            'cyclomatic_complexity' => [
                'value' => $this->cyclomaticComplexity,
                'level' => $this->getComplexityLevel($this->cyclomaticComplexity),
            ],
            'cognitive_complexity' => [
                'value' => $this->cognitiveComplexity,
                'level' => $this->getComplexityLevel($this->cognitiveComplexity),
            ],
            'complexity_level' => $this->getComplexityLevel(
                ($this->cyclomaticComplexity + $this->cognitiveComplexity) / 2
            ),
        ];
    }

    private function getComplexityLevel(float $value): string
    {
        if ($value <= 4) {
            return 'low';
        }

        if ($value <= 7) {
            return 'moderate';
        }

        if ($value <= 10) {
            return 'high';
        }

        return 'very-high';
    }
}
