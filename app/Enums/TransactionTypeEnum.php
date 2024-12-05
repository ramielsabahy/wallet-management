<?php

namespace App\Enums;

enum TransactionTypeEnum: string
{
    case DEPOSIT = "deposit";
    case WITHDRAWAL = "withdrawal";
    case OUTGOING_TRANSFER = "outgoing_transfer";
    case INCOMING_TRANSFER = "incoming_transfer";
}
