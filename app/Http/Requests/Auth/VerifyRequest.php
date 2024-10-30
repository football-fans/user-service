<?php

namespace App\Http\Requests\Auth;

use App\Enums\VerificationType;
use App\Http\Requests\BaseRequest;
use App\Http\Requests\Traits\HandlesVerificationInput;

class VerifyRequest extends BaseRequest
{
    use HandlesVerificationInput;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'token' => 'required|string',
            'code' => 'required|string|digits:6',
            'identifier' => 'required|string',
            'type' => 'nullable',
        ];
    }
}
