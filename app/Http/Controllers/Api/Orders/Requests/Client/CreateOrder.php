<?php

namespace App\Http\Controllers\Api\Orders\Requests\Client;

use Illuminate\Validation\Rule;
use App\Http\Requests\ApiRequest;
use Project\Common\CountryCodeIso3166;
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