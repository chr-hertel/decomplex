<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use PUGX\Shortid\Shortid;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DiffRepository")
 */
class Diff
{
    /**
     * @ORM\Id
     * @ORM\Column(length=6)
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private string $id;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private \DateTimeImmutable $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Snippet", fetch="EAGER", cascade={"persist"})
     */
    private Snippet $snippetLeft;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Snippet", fetch="EAGER", cascade={"persist"})
     */
    private Snippet $snippetRight;

    public function __construct(Snippet $snippetLeft, Snippet $snippetRight)
    {
        $this->id = (string) Shortid::generate(6);
        $this->createdAt = new \DateTimeImmutable();
        $this->snippetLeft = $snippetLeft;
        $this->snippetRight = $snippetRight;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getSnippetLeft(): Snippet
    {
        return $this->snippetLeft;
    }

    public function getSnippetRight(): Snippet
    {
        return $this->snippetRight;
    }
}
