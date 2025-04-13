<?php

namespace App\Http\Requests;

use App\Enums\OrderStatusEnum;
use App\Helpers\Enum;
use App\Models\Member;
use App\Models\Routes;
use App\Models\Payment;
use Illuminate\Foundation\Http\FormRequest;

class PaymentUpdateRequest extends FormRequest
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
        return [
            'name' => 'string| max:1000',
            'photo' => $this->hasFile('photo')
            ? 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            : 'nullable|string',
            'acc_name' => 'string| max:1000',
            'acc_number' => 'numeric',
            'acc_qr' => $this->hasFile('acc_qr')
                ? 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
                : 'nullable|string',
            ];
    }
}
