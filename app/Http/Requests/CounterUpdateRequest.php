<?php

namespace App\Http\Requests;

use App\Enums\GeneralStatusEnum;
use App\Helpers\Enum;
use App\Models\Counter;
use Illuminate\Foundation\Http\FormRequest;

class CounterUpdateRequest extends FormRequest
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
        $counter = Counter::findOrFail(request('id'));
        $counterId = $counter->id;

        return [
            'name' => "required|string| unique:counters,name,$counterId| max:24 | min:1",
            'phone' => "nullable|min:9|max:13",
            'city' => 'string| nullable| max:1000',        
            'terminal' => 'string| nullable| max:1000',            
            'status' => "required|in:$enum"
        ];
    }
}
