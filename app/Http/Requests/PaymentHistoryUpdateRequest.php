<?php

namespace App\Http\Requests;

use App\Enums\OrderStatusEnum;
use App\Helpers\Enum;
use App\Models\Member;
use App\Models\Routes;
use App\Models\Payment;
use Illuminate\Foundation\Http\FormRequest;

class PaymentHistoryUpdateRequest extends FormRequest
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
        $payments = Payment::all()->pluck('id')->toArray();
        $payments = implode(',', $payments);
        $routes = Routes::all()->pluck('id')->toArray();
        $routes = implode(',', $routes);
        $enum = implode(',', (new Enum(OrderStatusEnum::class))->values());

        return [
            'phone' => 'min:1|max:13',
            'nrc' => 'string| max:1000',
            'seat' => 'nullable| json',
            'total' => 'numeric',
            'note' => 'string| nullable| max:1000',
            'start_time' => 'nullable',
            'member_id' => "nullable|in:$members",
            'kpay_member_id' => 'nullable',
            'route_id' => "required|in:$routes",
            'screenshot' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'payment_id' => "nullable|in:$payments",
            'status' => "required|in:$enum"
        ];
    }
}
