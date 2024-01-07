<?php

namespace App\Http\Controllers\Api\Promotions\Requests;

use App\Http\Requests\ApiRequest;
use Project\Modules\Shopping\Discounts\Promotions\Commands\UpdatePromotionCommand;

class UpdatePromotionRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'id' => 'bail|required|numeric|integer|exists:shopping_discounts_promotions,id',
            'name' => 'required|string',
            'startDate' => 'nullable|date',
            'endDate' => 'nullable|date',
        ];
    }

    public function getCommand(): UpdatePromotionCommand
    {
        $validated = $this->validated();
        return new UpdatePromotionCommand(
            $validated['id'],
            $validated['name'],
            !empty($validated['startDate'])
                ? new \DateTimeImmutable($validated['startDate'])
                : null,
            !empty($validated['endDate'])
                ? new \DateTimeImmutable($validated['endDate'])
                : null,
        );
    }
}