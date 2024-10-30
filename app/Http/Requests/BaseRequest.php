<?php

namespace App\Http\Requests;

use App\Services\ResponseService;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class BaseRequest extends FormRequest
{
    protected $responseService;
    public function __construct(){
        $this->responseService = app(ResponseService::class);
    }
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [

        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $error = $validator->errors()->first();  // Get just the error messages as an array
        $response = $this->responseService->error($error);

        throw new HttpResponseException($response);
    }
}
