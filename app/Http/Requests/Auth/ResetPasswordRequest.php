<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseRequest;
use App\Http\Requests\Traits\HandlesVerificationInput;
use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends BaseRequest
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
            'identifier' => 'required|string',
            'type' => 'nullable',
            'token' => 'required|string',
            'password' => 'required|string',
        ];
    }
}
