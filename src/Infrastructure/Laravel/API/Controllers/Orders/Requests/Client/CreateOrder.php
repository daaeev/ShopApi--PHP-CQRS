<?php

namespace Project\Infrastructure\Laravel\API\Controllers\Orders\Requests\Client;

use Illuminate\Validation\Rule;
use Project\Common\Utils\CountryCodeIso3166;
use Project\Infrastructure\Laravel\API\Utils\ApiRequest;
use Project\Modules\Shopping\Api\DTO\Order\DeliveryInfo;
use Project\Modules\Shopping\Order\Commands\CreateOrderCommand;
use Project\Modules\Shopping\Order\Entity\Delivery\DeliveryService;

class CreateOrder extends ApiRequest
{
    public function rules(): array
    {
        return [
            'firstName' => 'required|string',
            'lastName' => 'required|string',
            'phone' => 'required|string|phone:INTERNATIONAL,' . CountryCodeIso3166::UKRAINE,
            'email' => 'nullable|email',
            'delivery' => 'required|array',
            'delivery.service' => ['required', Rule::in(DeliveryService::values())],
            'delivery.country' => 'required|string',
            'delivery.city' => 'required|string',
            'delivery.street' => 'required|string',
            'delivery.houseNumber' => 'required|string',
            'customerComment' => 'nullable|string',
        ];
    }

    public function getCommand(): CreateOrderCommand
    {
        $validated = $this->validated();
        return new CreateOrderCommand(
            $validated['firstName'],
            $validated['lastName'],
            $validated['phone'],
            $validated['email'],
            new DeliveryInfo(
                $validated['delivery']['service'],
                $validated['delivery']['country'],
                $validated['delivery']['city'],
                $validated['delivery']['street'],
                $validated['delivery']['houseNumber'],
            ),
            $validated['customerComment'],
        );
    }
}