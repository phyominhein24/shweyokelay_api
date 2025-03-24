<?php

namespace App\Http\Requests;

use App\Enums\GeneralStatusEnum;
use App\Helpers\Enum;
use App\Models\Member;
use App\Models\Routes;
use Illuminate\Foundation\Http\FormRequest;

class PaymentHistoryStoreRequest extends FormRequest
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
        $members = Member::all()->pluck('id')->toArray();
        $members = implode(',', $members);
        $routes = Routes::all()->pluck('id')->toArray();
        $routes = implode(',', $routes);
        $enum = implode(',', (new Enum(GeneralStatusEnum::class))->values());

        return [
            'phone' => 'min:9|max:13',
            'nrc' => 'string| max:1000',
            'seat' => 'nullable| json',
            'total' => 'numeric| max:1000',
            'note' => 'string| nullable| max:1000',
            'start_time' => 'timestamp| nullable',
            'member' => "nullable|in:$members",
            'route' => "required|in:$routes",
            'status' => "required|in:$enum"
        ];
    }
}
