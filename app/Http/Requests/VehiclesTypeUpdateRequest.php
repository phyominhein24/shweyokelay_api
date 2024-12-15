<?php

namespace App\Http\Requests;

use App\Enums\GeneralStatusEnum;
use App\Helpers\Enum;
use App\Models\VehiclesType;
use Illuminate\Foundation\Http\FormRequest;

class VehiclesTypeUpdateRequest extends FormRequest
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
        $vehicles_types = VehiclesType::findOrFail(request('id'));
        $vehicles_typesId = $vehicles_types->id;

        return [
            'name' => "required|string| unique:vehicles_types,name,$vehicles_typesId| max:24 | min:1",
            'seat_layout' => "required|string",
            'total_seat' => 'nullable|numeric|min:0|max:1000',
            'facilities' => 'string| json',            
            'status' => "required|in:$enum"
        ];
    }
}
