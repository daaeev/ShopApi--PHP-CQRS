<?php

namespace App\Http\Controllers\Api\Product\Requests;

use Illuminate\Validation\Rule;
use App\Http\Requests\ApiRequest;
use Project\Modules\Product\Api\DTO;
use Project\Modules\Product\Entity\Size\Size;
use Project\Modules\Product\Entity\Availability;
use Project\Modules\Product\Entity\Price\Currency;
use Project\Modules\Product\Entity\Color\ColorTypeMapper;
use Project\Modules\Product\Commands\UpdateProductCommand;

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
            'colors.*' => 'array',
            'colors.*.color' => 'required|string',
            'colors.*.type' => ['required', Rule::in(ColorTypeMapper::getTypes())],

            'sizes' => 'nullable|array',
            'sizes.*' => ['required', Rule::in(Size::values())],

            'prices' => 'required|array',
            'prices.*' => 'array',
            'prices.*.currency' => ['required', Rule::in(Currency::active())],
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
            array_map(function (array $color) {
                return new DTO\Color(
                    $color['color'],
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