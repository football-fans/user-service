<?php

namespace App\Enums\Traits;

trait EnumHelperTrait
{
    public static function implode($separator = ',')
    {
        return implode($separator, array_map(fn($case) => $case->value, self::cases()));
    }
}
