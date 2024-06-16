<?php

namespace App\Http\Controllers\Api\Orders\Requests;

use Illuminate\Validation\Rule;
use App\Http\Requests\ApiRequest;
use Project\Modules\Shopping\Order\Entity\OrderStatus;
use Project\Modules\Shopping\Order\Entity\PaymentStatus;
use Project\Modules\Shopping\Order\Queries\GetOrdersQuery;

class GetOrders extends ApiRequest
{
    public function rules(): array
    {
        return [
            'page' => 'nullable|numeric|integer|min:0',
            'limit' => 'nullable|numeric|integer|min:0',

            'filters' => 'nullable|array',
            'filters.priceFrom' => 'nullable|numeric|integer|min:0',
            'filters.priceTo' => 'nullable|numeric|integer',
            'filters.clientId' => 'bail|nullable|numeric|integer|exists:clients,id',
            'filters.clientHash' => 'nullable|string',
            'filters.phone' => 'nullable|string',
            'filters.email' => 'nullable|string',
            'filters.name' => 'nullable|string',
            'filters.status' => ['nullable', Rule::in(OrderStatus::values())],
            'filters.paymentsStatus' => ['nullable', Rule::in(PaymentStatus::values())],

            'sorting' => 'nullable|array:id,totalPrice,regularPrice,createdAt',
            'sorting.*' => Rule::in(['asc', 'desc']),
        ];
    }

    public function getQuery(): GetOrdersQuery
    {
        $validated = $this->validated();
        return new GetOrdersQuery(
            $validated['page'] ?? 1,
            $validated['limit'] ?? 1,
            $validated['filters'] ?? [],
            $validated['sorting'] ?? [],
        );
    }
}