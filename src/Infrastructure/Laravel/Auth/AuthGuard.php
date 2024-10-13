<?php

namespace Project\Infrastructure\Laravel\Auth;

enum AuthGuard: string
{
    case ADMIN = 'admin';
    case CLIENT = 'client';
}
