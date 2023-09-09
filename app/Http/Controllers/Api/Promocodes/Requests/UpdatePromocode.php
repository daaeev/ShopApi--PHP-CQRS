<?php

namespace App\Http\Controllers\Api\Promocodes\Requests;

use App\Http\Requests\ApiRequest;
use Project\Modules\Shopping\Discounts\Promocodes\Commands\UpdatePromocodeCommand;

class UpdatePromocode extends ApiRequest
{
    public function rules(): array
    {
        return [
            'id' => 'bail|required|numeric|integer|exists:shopping_discounts_promocodes,id',
            'name' => 'required|string',
            'startDate' => 'required|date',
            'endDate' => 'nullable|date',
        ];
    }

    public function getCommand(): UpdatePromocodeCommand
    {
        $validated = $this->validated();
        return new UpdatePromocodeCommand(
            $validated['id'],
            $validated['name'],
            new \DateTimeImmutable($validated['startDate']),
            !empty($validated['endDate'])
                ? new \DateTimeImmutable($validated['endDate'])
                : null
        );
    }
}