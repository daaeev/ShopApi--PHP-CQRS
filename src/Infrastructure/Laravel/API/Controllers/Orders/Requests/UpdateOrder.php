<?php

namespace Project\Infrastructure\Laravel\API\Controllers\Orders\Requests;

use Illuminate\Validation\Rule;
use Project\Common\Utils\CountryCodeIso3166;
use Project\Modules\Shopping\Order\Entity\OrderStatus;
use Project\Infrastructure\Laravel\API\Utils\ApiRequest;
use Project\Modules\Shopping\Api\DTO\Order\DeliveryInfo;
use Project\Modules\Shopping\Order\Entity\PaymentStatus;
use Project\Modules\Shopping\Order\Commands\UpdateOrderCommand;
use Project\Modules\Shopping\Order\Entity\Delivery\DeliveryService;

class UpdateOrder extends ApiRequest
{
    public function rules(): array
    {
        return [
            'id' => 'bail|required|numeric|integer|exists:shopping_orders,id',
            'firstName' => 'required|string',
            'lastName' => 'required|string',
            'phone' => 'required|string|phone:INTERNATIONAL,' . CountryCodeIso3166::UKRAINE,
            'email' => 'nullable|email',
            'status' => ['required', Rule::in(OrderStatus::values())],
            'paymentStatus' => ['required', Rule::in(PaymentStatus::values())],
            'delivery' => 'required|array',
            'delivery.service' => ['required', Rule::in(DeliveryService::values())],
            'delivery.country' => 'required|string',
            'delivery.city' => 'required|string',
            'delivery.street' => 'required|string',
            'delivery.houseNumber' => 'required|string',
            'managerComment' => 'nullable|string',
        ];
    }

    public function getCommand(): UpdateOrderCommand
    {
        $validated = $this->validated();
        return new UpdateOrderCommand(
            $validated['id'],
            $validated['firstName'],
            $validated['lastName'],
            $validated['phone'],
            $validated['email'],
            $validated['status'],
            $validated['paymentStatus'],
            new DeliveryInfo(
                $validated['delivery']['service'],
                $validated['delivery']['country'],
                $validated['delivery']['city'],
                $validated['delivery']['street'],
                $validated['delivery']['houseNumber'],
            ),
            $validated['managerComment'],
        );
    }
}