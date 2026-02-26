<?php

namespace App\Enum;

use App\Enum\Traits\EnumToArray;

enum EventTypeEnum: string
{
    use EnumToArray;

    case TRANSFER = 'transfer';
    case WITHDRAW = 'withdraw';
    case DEPOSIT = 'deposit';
}
