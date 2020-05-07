<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ZeroSum implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $total = array_reduce($value, function ($total, $point) {
            return $total += $point;
        }, 0);
        return $total === 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return '合計が0ではありません。';
    }
}
