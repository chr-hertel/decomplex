<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

#[ORM\Entity]
class Snippet implements JsonSerializable
{
    #[ORM\Id, ORM\Column(type: 'integer'), ORM\GeneratedValue(strategy: 'SEQUENCE')]
    private int $id;

    public function __construct(
        #[ORM\Column(type: 'text')]
        private readonly string $code,
        #[ORM\Column(unique: true)]
        private readonly string $hash,
        #[ORM\Column(type: 'integer')]
        private readonly int $cyclomaticComplexity,
        #[ORM\Column(type: 'integer')]
        private readonly int $cognitiveComplexity,
    ) {
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getComplexityLevel(): string
    {
        return $this->determineComplexityLevel(
            ($this->getCyclomaticComplexity() + $this->getCognitiveComplexity()) / 2
        );
    }

    public function getCyclomaticComplexity(): int
    {
        return $this->cyclomaticComplexity;
    }

    public function getCyclomaticComplexityLevel(): string
    {
        return $this->determineComplexityLevel($this->getCyclomaticComplexity());
    }

    public function getCognitiveComplexity(): int
    {
        return $this->cognitiveComplexity;
    }

    public function getCognitiveComplexityLevel(): string
    {
        return $this->determineComplexityLevel($this->getCognitiveComplexity());
    }

    /**
     * @return array{
     *     cyclomatic_complexity: array{value: integer, level: string},
     *     cognitive_complexity: array{value: integer, level: string},
     *     complexity_level: string
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'cyclomatic_complexity' => [
                'value' => $this->getCyclomaticComplexity(),
                'level' => $this->getCyclomaticComplexityLevel(),
            ],
            'cognitive_complexity' => [
                'value' => $this->getCognitiveComplexity(),
                'level' => $this->getCognitiveComplexityLevel(),
            ],
            'complexity_level' => $this->getComplexityLevel(),
        ];
    }

    private function determineComplexityLevel(float $value): string
    {
        return match (true) {
            $value < 4 => 'low',
            $value < 7 => 'moderate',
            $value < 10 => 'high',
            $value < 100 => 'very-high',
            default => 'overkill',
        };
    }
}
