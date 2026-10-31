<?php

namespace App\Enums;

enum InvoiceStatus: string
{
    case Draft     = 'draft';
    case Issued    = 'issued';
    case Partial   = 'partial';
    case Paid      = 'paid';
    case Overdue   = 'overdue';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::Draft     => 'Draft',
            self::Issued    => 'Diterbitkan',
            self::Partial   => 'Sebagian Dibayar',
            self::Paid      => 'Lunas',
            self::Overdue   => 'Jatuh Tempo',
            self::Cancelled => 'Dibatalkan',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::Draft     => 'bg-gray-100 text-gray-700',
            self::Issued    => 'bg-blue-100 text-blue-700',
            self::Partial   => 'bg-yellow-100 text-yellow-700',
            self::Paid      => 'bg-green-100 text-green-700',
            self::Overdue   => 'bg-red-100 text-red-700',
            self::Cancelled => 'bg-gray-100 text-gray-500',
        };
    }
}
