<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Helpers\Enum;
use App\Models\Routes;
use App\Enums\GeneralStatusEnum;

class DailyRouteUpdate extends FormRequest
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
        $routes = Routes::all()->pluck('id')->toArray();
        $routes = implode(',', $routes);
        $enum = implode(',', (new Enum(GeneralStatusEnum::class))->values());

        return [
            'driver_name' => 'string| nullable| max:1000',
            'car_no' => 'string| nullable| max:1000',
            // 'route' => "required|in:$routes",
            // 'start_date' => 'timestamp| required',
            'status' => "required|in:$enum"
        ];
    }
}
