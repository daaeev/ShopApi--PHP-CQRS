<?php

namespace App\Http\Controllers\Api\Promocodes\Requests;

use App\Http\Requests\ApiRequest;
use Project\Modules\Shopping\Discounts\Promocodes\Commands\CreatePromocodeCommand;

class CreatePromocode extends ApiRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'code' => 'required|string|unique:shopping_discounts_promocodes,code',
            'discountPercent' => 'required|numeric|integer|min:1|max:100',
            'startDate' => 'required|date',
            'endDate' => 'nullable|date',
        ];
    }

    public function getCommand(): CreatePromocodeCommand
    {
        $validated = $this->validated();
        return new CreatePromocodeCommand(
            $validated['name'],
            $validated['code'],
            $validated['discountPercent'],
            new \DateTimeImmutable($validated['startDate']),
            !empty($validated['endDate'])
                ? new \DateTimeImmutable($validated['endDate'])
                : null
        );
    }
}