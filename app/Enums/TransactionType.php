<?php

namespace App\Enums;

enum TransactionType: string
{
    case INCOME = 'income';
    case EXPENSE = 'expense';
    case TRANSFER = 'transfer';

    public function label(): string
    {
        return match ($this) {
            self::INCOME => 'Pemasukan',
            self::EXPENSE => 'Pengeluaran',
            self::TRANSFER => 'Transfer',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::INCOME => 'green',
            self::EXPENSE => 'red',
            self::TRANSFER => 'blue',
        };
    }
}
