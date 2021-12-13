<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Diff;
use App\Entity\Snippet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Diff|null findOneBy(array $criteria, array $orderBy = null)
 *
 * @extends ServiceEntityRepository<Diff>
 */
class DiffRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Diff::class);
    }

    /**
     * @return array<int, Diff>
     */
    public function findLatest(): array
    {
        return $this->findBy([], ['createdAt' => 'DESC'], 10);
    }

    public function findOneBySnippets(Snippet $leftSnippet, Snippet $rightSnippet): ?Diff
    {
        return $this->findOneBy([
            'snippetLeft' => $leftSnippet,
            'snippetRight' => $rightSnippet,
        ]);
    }
}
