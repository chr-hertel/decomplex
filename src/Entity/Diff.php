<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use PUGX\Shortid\Shortid;

/**
 * @ORM\Entity
 */
class Diff
{
    private const DEFAULT_CODE = '<?php'.PHP_EOL;

    /**
     * @ORM\Id
     * @ORM\Column(length=6)
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private string $id;

    /**
     * @ORM\Column(type="text")
     */
    private string $codeSnippetLeft;

    /**
     * @ORM\Column(type="text")
     */
    private string $codeSnippetRight;

    public function __construct(string $codeSnippetLeft = self::DEFAULT_CODE, string $codeSnippetRight = self::DEFAULT_CODE)
    {
        $this->id = (string) Shortid::generate(6);
        $this->codeSnippetLeft = $codeSnippetLeft;
        $this->codeSnippetRight = $codeSnippetRight;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCodeSnippetLeft(): string
    {
        return $this->codeSnippetLeft;
    }

    public function getCodeSnippetRight(): string
    {
        return $this->codeSnippetRight;
    }
}
