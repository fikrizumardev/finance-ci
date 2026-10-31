<?php

namespace App\Enums;

enum BudgetPeriod: string
{
    case MONTHLY = 'monthly';
    case YEARLY = 'yearly';

    public function label(): string
    {
        return match ($this) {
            self::MONTHLY => 'Bulanan',
            self::YEARLY => 'Tahunan',
        };
    }
}
