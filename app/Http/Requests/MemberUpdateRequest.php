<?php

namespace App\Http\Requests;

use App\Enums\GeneralStatusEnum;
use App\Helpers\Enum;
use App\Models\Member;
use Illuminate\Foundation\Http\FormRequest;

class MemberUpdateRequest extends FormRequest
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
        $member = Member::findOrFail(request('id'));
        $memberId = $member->id;

        return [
            'name' => "required|string| unique:members,name,$memberId| max:24 | min:4",
            'email' => "required| email| unique:members,email,$memberId|string",
            'phone' => "nullable|unique:members,phone,$memberId|min:9|max:13",
            'is_agent' => 'nullable|boolean',            
            'commission' => 'nullable|string|not_in:""',
            'status' => "required|in:$enum"
        ];
    }
}
