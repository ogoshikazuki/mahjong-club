<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use App\Rules\ZeroSum;

class GameResultRequest extends FormRequest
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
            'rate' => ['required', 'numeric'],
            'points' => ['required', 'array', new ZeroSum],
            'tips' => ['array', new ZeroSum],
        ];
    }

    public function messages()
    {
        return [
            'rate.required' => 'レートを選択してください。',
            'points.required' => 'ポイントを入力してください。',
        ];
    }

    public function attributes()
    {
        return [
            'points' => 'ポイント',
            'tips' => '祝儀',
        ];
    }
}
