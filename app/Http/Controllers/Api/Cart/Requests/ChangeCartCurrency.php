<?php

namespace App\Http\Controllers\Api\Cart\Requests;

use Illuminate\Validation\Rule;
use App\Http\Requests\ApiRequest;
use Project\Common\Product\Currency;
use Project\Modules\Cart\Commands\ChangeCurrencyCommand;

class ChangeCartCurrency extends ApiRequest
{
    public function rules()
    {
        return [
            'currency' => [Rule::in(array_column(Currency::active(), 'value'))]
        ];
    }

    public function getCommand(): ChangeCurrencyCommand
    {
        $validated = $this->validated();

        return new ChangeCurrencyCommand($validated['currency']);
    }
}