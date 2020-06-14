<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Diff;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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
}
