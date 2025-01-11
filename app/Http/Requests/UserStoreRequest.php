<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\GeneralStatusEnum;
use App\Helpers\Enum;

class UserStoreRequest extends FormRequest
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
            'name' => 'required|string| unique:users,name| max:24 | min:4',
            'email' => 'required| email| unique:users,email|unique:members,email|string',
            'phone' => 'nullable|unique:users,phone|min:9|max:13',
            'password' => 'required| max:24 | min:6',
            'status' => "required|in:$enum"
        ];
    }
}
