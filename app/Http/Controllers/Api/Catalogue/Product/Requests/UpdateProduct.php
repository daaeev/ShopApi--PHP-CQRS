<?php

namespace App\Http\Controllers\Api\Catalogue\Product\Requests;

use Illuminate\Validation\Rule;
use App\Http\Requests\ApiRequest;
use Project\Common\Product\Currency;
use Project\Common\Product\Availability;
use Project\Modules\Catalogue\Api\DTO\Product as DTO;
use Project\Modules\Catalogue\Product\Commands\UpdateProductCommand;

class UpdateProduct extends ApiRequest
{
    public function rules()
    {
        return [
            'id' => 'bail|required|numeric|integer|exists:products,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255',
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

    public function getCommand(): UpdateProductCommand
    {
        $validated = $this->validated();

        return new UpdateProductCommand(
            $validated['id'],
            $validated['name'],
            $validated['code'],
            $validated['active'],
            $validated['availability'],
            $validated['colors'] ?? [],
            $validated['sizes'] ?? [],
            array_map(function (array $price) {
                return new DTO\Price(
                    $price['currency'],
                    $price['price'],
                );
            }, $validated['prices']),
        );
    }
}