<?php

declare(strict_types=1);

namespace App\ComplexityDiff;

use App\Entity\Diff;
use App\Repository\DiffRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;

class Persister
{
    public function __construct(
        private Calculator $calculator,
        private EntityManagerInterface $entityManager,
        private DiffRepository $diffRepository,
    ) {
    }

    public function persistDiff(string $leftCode, string $rightCode): Diff
    {
        $leftSnippet = $this->calculator->calculateComplexities($leftCode);
        $rightSnippet = $this->calculator->calculateComplexities($rightCode);

        if ($leftSnippet->equalTo($rightSnippet)) {
            $rightSnippet = $leftSnippet;
        }

        $diff = new Diff($leftSnippet, $rightSnippet);

        $this->entityManager->persist($diff);
        try {
            $this->entityManager->flush();
        } catch (UniqueConstraintViolationException $exception) {
            $diff = $this->diffRepository->findOneBySnippets($leftSnippet, $rightSnippet);

            if (null === $diff) {
                $message = 'Cannot find diff with given snippets (left: %s and right: %s)';

                throw new LogicException(sprintf($message, $leftSnippet->getHash(), $rightSnippet->getHash()));
            }
        }

        return $diff;
    }
}
