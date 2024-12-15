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

            'distance' => 'string| nullable| max:1000',
            'duration' => 'string| nullable| max:1000',

            'is_ac' => 'boolean| nullable',
            'day_off' => 'nullable| json',
            'start_date' => 'timestamp| nullable',
            'price' => 'string| nullable| max:1000',
            'departure' => 'timestamp',
            'arrivals' => 'timestamp',

            'starting_point' => "required|in:$counters",
            'ending_point' => "required|in:$counters|different:starting_point",
        
            'vehicles_type' => "required|in:$vehicles",
            'status' => "required|in:$enum"
        ];
    }
}
