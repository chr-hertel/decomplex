<?php

namespace App\Helper;

use Exception;

class StringUtil
{
    public function camelCase(string $string): string
    {
        if ('' === $string) {
            throw new Exception('Cannot transform an empty string');
        }

        return str_replace(' ', '', preg_replace_callback('/\b./u', static function ($m) use (&$i) {
            return 1 === ++$i ? ('İ' === $m[0] ? 'i̇' : mb_strtolower($m[0], 'UTF-8')) : mb_convert_case($m[0], MB_CASE_TITLE, 'UTF-8');
        }, preg_replace('/[^\pL0-9]++/u', ' ', $string)));
    }

    public function lowerCamelCase(string $string): string
    {
        return lcfirst($this->camelCase($string));
    }
}
