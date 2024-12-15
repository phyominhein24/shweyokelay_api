<?php

namespace App\Http\Requests;

use App\Enums\GeneralStatusEnum;
use App\Helpers\Enum;
use Illuminate\Foundation\Http\FormRequest;

class MemberStoreRequest extends FormRequest
{
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
        $enum = implode(',', (new Enum(GeneralStatusEnum::class))->values());

        return [
            'name' => 'required|string| unique:members,name| max:24 | min:1',
            'email' => 'required| email| unique:members,email|string',
            'phone' => 'nullable|unique:members,phone|min:9|max:13',
            'password' => 'required| max:24 | min:6',
            'agent' => 'boolean| nullable| max:1000',
            'per' => 'required|string',
            'status' => "required|in:$enum"
        ];
    }
}
