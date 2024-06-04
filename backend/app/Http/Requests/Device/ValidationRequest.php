<?php

namespace App\Http\Requests\Device;

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
            'devices' => 'required|array',
            'devices.*.model_number' => 'required|string',
            'devices.*.device_id' => 'required|string',
            'devices.*.company_id' => 'required|integer',

            'devices.*.created_at' => 'required',

        ];
    }

    public function messages()
    {
        return [
            'devices.required' => 'Devices array is required.',
            'devices.array' => 'Devices must be an array.',
            'devices.*.model_number.required' => 'The model number is required for each device.',
            'devices.*.model_number.string' => 'The model number must be a string for each device.',
            'devices.*.device_id.required' => 'The device ID is required for each device.',
            'devices.*.device_id.string' => 'The device ID must be a string for each device.',
            'devices.*.company_id.required' => 'The company ID is required for each device.',
            'devices.*.company_id.integer' => 'The company ID must be an integer for each device.',
        ];
    }
}
