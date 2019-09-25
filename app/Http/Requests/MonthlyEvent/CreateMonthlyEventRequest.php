<?php

namespace Custodia\Http\Requests\MonthlyEvent;

use Illuminate\Foundation\Http\FormRequest;

class CreateMonthlyEventRequest extends FormRequest
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
            'title' => 'required',
            'month' => 'required',
        ];
    }
}
