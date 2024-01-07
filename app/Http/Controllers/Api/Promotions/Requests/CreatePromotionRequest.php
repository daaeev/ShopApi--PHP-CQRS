<?php

namespace App\Http\Controllers\Api\Promotions\Requests;

use Illuminate\Validation\Rule;
use App\Http\Requests\ApiRequest;
use Project\Modules\Shopping\Discounts\Promotions\Commands\CreatePromotionCommand;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\DiscountType;

class CreatePromotionRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'startDate' => 'nullable|date',
            'endDate' => 'nullable|date',
            'disabled' => 'required|boolean',
            'discounts' => 'nullable|array',
            'discounts.*' => 'array',
            'discounts.*.type' => ['required', Rule::in(DiscountType::values())],
            'discounts.*.data' => ['required', 'array'],
        ];
    }

    public function getCommand(): CreatePromotionCommand
    {
        $validated = $this->validated();
        return new CreatePromotionCommand(
            $validated['name'],
            !empty($validated['startDate'])
                ? new \DateTimeImmutable($validated['startDate'])
                : null,
            !empty($validated['endDate'])
                ? new \DateTimeImmutable($validated['endDate'])
                : null,
            $validated['disabled'],
            $validated['discounts'] ?? []
        );
    }
}