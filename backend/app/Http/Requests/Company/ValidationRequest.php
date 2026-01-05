<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;

class ValidationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            "name"                => "required|min:3|max:100",
            "contact_person_name" => "required|min:3|max:50",
            "number"              => "required|min:3|max:20",
            "email"               => "required|min:5|max:100",
            "location"            => "nullable|min:5|max:150",

            'expiry_date' => ['required', 'date'],
            'license_key' => ['required', 'string', 'max:255'],

            'status' => ['nullable'],
            'machine_id' => ['nullable'],
        ];
    }
}
