<?php

namespace App\Http\Controllers\Api\Catalogue\Product\Requests;

use Illuminate\Validation\Rule;
use App\Http\Requests\ApiRequest;
use Project\Common\Product\Currency;
use Project\Common\Product\Availability;
use Project\Modules\Catalogue\Api\DTO;
use Project\Modules\Catalogue\Product\Commands\CreateProductCommand;

class CreateProduct extends ApiRequest
{
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'code' => 'bail|required|string|max:255|unique:catalogue_products,code',
            'active' => 'required|boolean',
            'availability' => ['required', Rule::in(Availability::values())],

            'colors' => 'nullable|array',
            'colors.*' => 'required|string',

            'sizes' => 'nullable|array',
            'sizes.*' => 'required|string',

            'prices' => 'required|array',
            'prices.*' => 'array',
            'prices.*.currency' => ['required', Rule::in(array_column(Currency::active(), 'value'))],
            'prices.*.price' => 'required|numeric',
        ];
    }

    public function getCommand(): CreateProductCommand
    {
        $validated = $this->validated();
        return new CreateProductCommand(
            $validated['name'],
            $validated['code'],
            $validated['active'],
            $validated['availability'],
            $validated['colors'] ?? [],
            $validated['sizes'] ?? [],
            array_map(function (array $price) {
                return new DTO\Product\Price(
                    $price['currency'],
                    $price['price'],
                );
            }, $validated['prices']),
        );
    }
}