<?php

namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;
use App\Models\Shop;
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
        $shops = Shop::all()->pluck('id')->toArray();
        $shops = implode(',', $shops);
        $enum = implode(',', (new Enum(GeneralStatusEnum::class))->values());
        $user = User::findOrFail(request('id'));
        $userId = $user->id;

        return [
            'name' => "required|string| unique:users,name,$userId| max:24 | min:4",
            'email' => "required| email| unique:users,email,$userId|string",
            'phone' => "nullable|unique:users,phone,$userId|min:9|max:13",
            'address' => 'string| nullable| max:100',            
            'shop_id' => "required|in:$shops",
            'status' => "required|in:$enum"
        ];
    }
}
