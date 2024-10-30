<?php

namespace App\Http\Requests\Traits;

use App\Enums\VerificationType;

trait HandlesVerificationInput
{
    protected function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $data = [];
            $identifier = $this->input('identifier');
            if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
                $data['type'] = VerificationType::EMAIL->value;
            } elseif (preg_match('/^\+?[1-9]\d{1,14}$/', $identifier)) {
                $data['type'] = VerificationType::PHONE->value;
            } else {
                $this->validator->errors()->add('identifier', 'The identifier is not a valid email address or phone number.');
            }
            $validator->setData(array_merge($validator->getData(), $data));
        });
    }
}
