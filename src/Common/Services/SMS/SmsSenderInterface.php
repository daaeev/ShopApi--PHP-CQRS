<?php

namespace Project\Common\Services\SMS;

interface SmsSenderInterface
{
    public function send(string $phone, string $message): void;
}