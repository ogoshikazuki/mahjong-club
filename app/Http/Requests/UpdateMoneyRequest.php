<?php

namespace App\Http\Requests;

use App\Rules\ZeroSum;
use Illuminate\Foundation\Http\FormRequest;

class UpdateMoneyRequest extends FormRequest
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
    public function rules()
    {
        return [
            'money' => [
                'required',
                'array',
                new ZeroSum(),
            ],
        ];
    }

    public function attributes()
    {
        return [
            'money' => '金額',
        ];
    }
}
