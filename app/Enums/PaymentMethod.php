<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Transfer     = 'transfer';
    case Autodebet    = 'autodebet';
    case Cash         = 'cash';
    case VirtualAccount = 'virtual_account';
    case Qris         = 'qris';
    case Other        = 'other';

    public function label(): string
    {
        return match($this) {
            self::Transfer      => 'Transfer Bank',
            self::Autodebet     => 'Auto Debet',
            self::Cash          => 'Tunai',
            self::VirtualAccount => 'Virtual Account',
            self::Qris          => 'QRIS',
            self::Other         => 'Lainnya',
        };
    }
}
