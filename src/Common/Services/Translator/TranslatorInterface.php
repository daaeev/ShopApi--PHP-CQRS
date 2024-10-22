<?php

namespace Project\Common\Services\Translator;

interface TranslatorInterface
{
    public function translate(string $key, string $default, array $params = []): string;
}