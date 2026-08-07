<?php

namespace App\Enums;

/**
 * Payment transaction states. Values match the legacy DB strings exactly
 * (the transaction.status column stores these literals). Note: the legacy
 * enum only allowed pending|success|failed|cancel; refunded is applied via
 * admin forceFill and isn't part of the column enum.
 */
enum TransactionStatus: string
{
    case Pending  = 'pending';
    case Success  = 'success';
    case Failed   = 'failed';
    case Cancel   = 'cancel';
    case Refunded = 'refunded';
}
