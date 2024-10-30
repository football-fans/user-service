<?php

namespace App\Enums;

use App\Enums\Traits\EnumHelperTrait;

enum VerificationType : string
{
    use EnumHelperTrait;
    case EMAIL = 'email';
    case PHONE = 'phone';

}
