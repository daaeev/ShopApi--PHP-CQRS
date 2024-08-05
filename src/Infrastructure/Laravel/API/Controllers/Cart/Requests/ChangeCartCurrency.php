<?php

namespace Project\Infrastructure\Laravel\API\Controllers\Cart\Requests;

use Illuminate\Validation\Rule;
use Project\Common\Product\Currency;
use Project\Infrastructure\Laravel\API\Utils\ApiRequest;
use Project\Modules\Shopping\Cart\Commands\ChangeCurrencyCommand;

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