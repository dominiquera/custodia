<?php

namespace Custodia\Http\Requests\WeatherTrigger;

use Illuminate\Foundation\Http\FormRequest;

class CreateWeatherTriggerRequest extends FormRequest
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
            'rule' => 'required',
        ];
    }
}
