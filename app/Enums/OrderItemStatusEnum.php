<?php

namespace App\Enums;

enum OrderItemStatusEnum: string
{
    case SELECTED = 'SELECTED';
    case ORDERED = 'ORDERED';
    case SUCCESS = 'SUCCESS';
}
