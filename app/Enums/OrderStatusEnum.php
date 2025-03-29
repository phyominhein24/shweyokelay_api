<?php

namespace App\Enums;

enum OrderStatusEnum: string
{
    case PENDING = 'PENDING';
    case SUCCESS = 'SUCCESS';
    case CANCLE = 'CANCLE';
    case REJECT = 'REJECT';
}
