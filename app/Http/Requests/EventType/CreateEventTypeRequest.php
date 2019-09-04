<?php

namespace App\Http\Requests\EventType;

use Illuminate\Foundation\Http\FormRequest;

class CreateEventTypeRequest extends FormRequest
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
     * @return array
     */
    public static function rules()
    {
        return [
            'name' => 'required',
            'short_description' => 'required',
            'long_description' => 'required',
        ];
    }
}
