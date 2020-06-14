<?php

declare(strict_types=1);

namespace App\ComplexityDiff;

use App\Entity\Diff;
use Doctrine\ORM\EntityManagerInterface;

class Persister
{
    private Calculator $calculator;
    private EntityManagerInterface $entityManager;

    public function __construct(Calculator $calculator, EntityManagerInterface $entityManager)
    {
        $this->calculator = $calculator;
        $this->entityManager = $entityManager;
    }

    public function persistDiff(string $leftCode, string $rightCode): Diff
    {
        $leftSnippet = $this->calculator->calculateComplexities($leftCode);
        $rightSnippet = $this->calculator->calculateComplexities($rightCode);

        $diff = new Diff($leftSnippet, $rightSnippet);

        $this->entityManager->persist($diff);
        $this->entityManager->flush();

        return $diff;
    }
}
