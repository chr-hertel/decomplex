<?php

declare(strict_types=1);

namespace App\DeComplex;

final class CodeHasher
{
    public function hash(string $code): string
    {
        return md5($code);
    }
}
