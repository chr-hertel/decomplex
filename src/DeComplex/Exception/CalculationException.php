<?php

declare(strict_types=1);

namespace App\DeComplex\Exception;

use DomainException;
use JsonSerializable;
use PhpParser\Error;
use PhpParser\ErrorHandler\Collecting;

final class CalculationException extends DomainException implements JsonSerializable
{
    public function __construct(public readonly Collecting $collecting)
    {
        parent::__construct('Unable to parse given source code');
    }

    /**
     * @phpstan-return array<array{line: int, message: string}>
     */
    public function jsonSerialize(): array
    {
        return array_map(static function (Error $error) {
            return [
                'line' => $error->getEndLine(),
                'message' => $error->getRawMessage(),
            ];
        }, $this->collecting->getErrors());
    }
}
