<?php

namespace Project\Modules\Client\Entity\Access;

use Webmozart\Assert\Assert;
use Project\Common\Utils\ContactsValidator;

class SocialAccess extends Access
{
    public function __construct(
        private readonly string $email,
        private readonly string $socialId,
    ) {
        ContactsValidator::validateEmail($this->email);
        Assert::notEmpty($this->socialId);
    }

    public function getType(): AccessType
    {
        return AccessType::SOCIAL;
    }

    public function getCredentials(): array
    {
        return ['email' => $this->email, 'socialId' => $this->socialId];
    }
}