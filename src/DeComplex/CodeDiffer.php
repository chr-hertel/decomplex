<?php

declare(strict_types=1);

namespace App\DeComplex;

use App\Entity\Diff;
use App\Repository\DiffRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;

final class CodeDiffer
{
    public function __construct(
        private readonly ComplexityCalculator $calculator,
        private readonly EntityManagerInterface $entityManager,
        private readonly DiffRepository $diffRepository,
    ) {
    }

    public function create(string $leftCode, string $rightCode): Diff
    {
        $leftSnippet = $this->calculator->analyze($leftCode);
        $rightSnippet = $leftCode === $rightCode ? $leftSnippet : $this->calculator->analyze($rightCode);

        $diff = new Diff($leftSnippet, $rightSnippet);

        $this->entityManager->persist($diff);
        try {
            $this->entityManager->flush();
        } catch (UniqueConstraintViolationException) {
            $diff = $this->diffRepository->findOneBySnippets($leftSnippet, $rightSnippet);

            if (null === $diff) {
                $message = 'Cannot find diff with given snippets (left: %s and right: %s)';

                throw new \LogicException(sprintf($message, $leftSnippet->getHash(), $rightSnippet->getHash()));
            }
        }

        return $diff;
    }
}
