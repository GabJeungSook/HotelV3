<?php

namespace App\Enums;

enum TransactionStatus: string
{
    case Draft = 'draft';
    case Held = 'held';
    case Completed = 'completed';
    case Voided = 'voided';
}
