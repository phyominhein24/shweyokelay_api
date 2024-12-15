<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Shop;
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
        $shops = Shop::all()->pluck('id')->toArray();
        $shops = implode(',', $shops);
        $enum = implode(',', (new Enum(GeneralStatusEnum::class))->values());

        return [
            'name' => 'required|string| unique:users,name| max:24 | min:4',
            'email' => 'required| email| unique:users,email|string',
            'phone' => 'nullable|unique:users,phone|min:9|max:13',
            'password' => 'required| max:24 | min:6',
            'address' => 'string| nullable| max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'shop_id' => "required|in:$shops",
            'status' => "required|in:$enum"
        ];
    }
}
