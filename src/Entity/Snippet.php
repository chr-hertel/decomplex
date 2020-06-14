<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Snippet implements \JsonSerializable
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     *
     * @psalm-suppress PropertyNotSetInConstructor
     */
    private int $id;

    /**
     * @ORM\Column(unique=true)
     */
    private string $hash;

    /**
     * @ORM\Column(type="text")
     */
    private string $code;

    /**
     * @ORM\Column(type="integer")
     */
    private int $cyclomaticComplexity;

    /**
     * @ORM\Column(type="integer")
     */
    private int $cognitiveComplexity;

    public function __construct(string $code, int $cyclomaticComplexity, int $cognitiveComplexity)
    {
        $this->hash = md5($code);
        $this->code = $code;
        $this->cyclomaticComplexity = $cyclomaticComplexity;
        $this->cognitiveComplexity = $cognitiveComplexity;
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

    public function equalTo(Snippet $rightSnippet): bool
    {
        return $this->hash === $rightSnippet->hash;
    }

    /**
     * @return array<string, array|string>
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
