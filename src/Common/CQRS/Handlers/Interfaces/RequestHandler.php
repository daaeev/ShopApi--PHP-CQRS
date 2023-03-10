<?php

namespace Project\Common\CQRS\Handlers\Interfaces;

interface RequestHandler
{
    public function handle(object $command): mixed;
}