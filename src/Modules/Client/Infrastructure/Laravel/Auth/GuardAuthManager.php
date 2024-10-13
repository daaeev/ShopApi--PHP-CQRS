<?php

namespace Project\Modules\Client\Infrastructure\Laravel\Auth;

use Project\Modules\Client\Entity\Client;
use Project\Modules\Client\Entity\ClientId;
use Illuminate\Contracts\Auth\StatefulGuard;
use Project\Modules\Client\Entity\Access\Access;
use Project\Modules\Client\Auth\AuthManagerInterface;
use Project\Modules\Client\Repository\ClientsRepositoryInterface;
use Project\Modules\Client\Infrastructure\Laravel\Models as Eloquent;

class GuardAuthManager implements AuthManagerInterface
{
    public function __construct(
        private readonly StatefulGuard $guard,
        private readonly ClientsRepositoryInterface $clients,
    ) {}

    public function authorize(Access $access): void
    {
        if ($this->guard->check()) {
            throw new \DomainException('Client already authenticated');
        }

        $client = Eloquent\Client::query()->hasAccess($access)->first();
        if (empty($client)) {
            throw new \DomainException('Credentials does not match to any client');
        }

        $this->guard->loginUsingId($client->id);
    }

    public function logout(): void
    {
        if (false === $this->guard->check()) {
            throw new \DomainException('Client does not authenticated');
        }

        $this->guard->logout();
    }

    public function logged(): ?Client
    {
        if (false === $this->guard->check()) {
            return null;
        }

        return $this->clients->get(ClientId::make($this->guard->id()));
    }
}