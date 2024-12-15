<?php

namespace App\Enums;

enum RoleEnum: string
{
    case SUPER_ADMIN = 'SUPER_ADMIN';
    case MANAGER = 'MANAGER';
    case WAITER = 'WAITER';
}
