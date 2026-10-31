<?php

namespace App\Enums;

enum AccountType: string
{
    case CASH = 'cash';
    case BANK = 'bank';
    case E_WALLET = 'e-wallet';
    case INVESTMENT = 'investment';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::CASH => 'Kas/Tunai',
            self::BANK => 'Bank',
            self::E_WALLET => 'E-Wallet',
            self::INVESTMENT => 'Investasi',
            self::OTHER => 'Lainnya',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::CASH => 'cash',
            self::BANK => 'bank',
            self::E_WALLET => 'wallet',
            self::INVESTMENT => 'chart',
            self::OTHER => 'folder',
        };
    }
}
