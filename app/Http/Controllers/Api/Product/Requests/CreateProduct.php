<?php

namespace App\Http\Controllers\Api\Product\Requests;

use Project\Common\Currency;
use Illuminate\Validation\Rule;
use App\Http\Requests\ApiRequest;
use Project\Modules\Product\Api\DTO;
use Project\Modules\Product\Entity\Size\ClotheSize;
use Project\Modules\Product\Entity\Availability;
use Project\Modules\Product\Entity\Color\ColorTypeMapper;
use Project\Modules\Product\Commands\CreateProductCommand;

class CreateProduct extends ApiRequest
{
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255',
            'active' => 'required|boolean',
            'availability' => ['required', Rule::in(Availability::values())],

            'colors' => 'nullable|array',
            'colors.*' => 'array',
            'colors.*.color' => 'required|string',
            'colors.*.name' => 'required|string',
            'colors.*.type' => ['required', Rule::in(ColorTypeMapper::getTypes())],

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
            array_map(function (array $color) {
                return new DTO\Color(
                    $color['color'],
                    $color['name'],
                    $color['type'],
                );
            }, $validated['colors'] ?? []),
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