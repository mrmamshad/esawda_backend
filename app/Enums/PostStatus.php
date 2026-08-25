<?php

namespace App\Enums;

/**
 * Ad/post lifecycle states. Values match the legacy DB strings exactly
 * (the product.status column stores these literals), so assigning an
 * enum into a query/forceFill is transparent.
 */
enum PostStatus: string
{
    case Active = 'active';
    case Pending = 'pending';
    case Expire = 'expire';
    case SoldOut = 'sold_out';
    case Removed = 'removed';
    case Draft = 'draft';
    case Rejected = 'rejected';

    /** All states the shop dashboard reports per-status counts for. */
    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }
}
