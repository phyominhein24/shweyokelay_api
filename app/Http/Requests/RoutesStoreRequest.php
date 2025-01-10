<?php

namespace App\Http\Requests;

use App\Enums\GeneralStatusEnum;
use App\Helpers\Enum;
use App\Models\Counter;
use App\Models\VehiclesType;
use Illuminate\Foundation\Http\FormRequest;

class RoutesStoreRequest extends FormRequest
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
        $vehicles = VehiclesType::all()->pluck('id')->toArray();
        $vehicles = implode(',', $vehicles);
        $counters = Counter::all()->pluck('id')->toArray();
        $counters = implode(',', $counters);
        $enum = implode(',', (new Enum(GeneralStatusEnum::class))->values());

        return [
            'name' => 'required|string| unique:routes,name| max:24 | min:4',

            'distance' => 'string| required',
            'duration' => 'string| required',

            'is_ac' => 'boolean| required',
            'day_off' => 'nullable| json',
            'start_date' => 'timestamp| nullable',
            'price' => 'string| required',
            'departure' => 'string| required',
            'arrivals' => 'string| required',

            'starting_point' => "required|in:$counters",
            'ending_point' => "required|in:$counters|different:starting_point",
        
            'vehicles_type_id' => "required|in:$vehicles",
            'status' => "required|in:$enum"
        ];
    }
}
