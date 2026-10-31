<?php

namespace App\Enums;

enum DebtStatus: string
{
    case UNPAID = 'unpaid';
    case PARTIAL = 'partial';
    case PAID = 'paid';

    public function label(): string
    {
        return match ($this) {
            self::UNPAID => 'Belum Lunas',
            self::PARTIAL => 'Cicilan',
            self::PAID => 'Lunas',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::UNPAID => 'red',
            self::PARTIAL => 'yellow',
            self::PAID => 'green',
        };
    }
}
