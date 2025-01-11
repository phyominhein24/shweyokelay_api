<?php

namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;
use App\Enums\GeneralStatusEnum;
use App\Helpers\Enum;

class UserUpdateRequest extends FormRequest
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
        $user = User::findOrFail(request('id'));
        $userId = $user->id;

        return [
            'name' => "required|string| unique:users,name,$userId| max:24 | min:4",
            'email' => "required| email|email,unique:members,email| unique:users,$userId|string",
            'phone' => "nullable|unique:users,phone,$userId|min:9|max:13",
            'status' => "required|in:$enum"
        ];
    }
}
