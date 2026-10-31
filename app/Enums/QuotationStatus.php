<?php

namespace App\Enums;

enum QuotationStatus: string
{
    case Draft    = 'draft';
    case Sent     = 'sent';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Expired  = 'expired';

    public function label(): string
    {
        return match($this) {
            self::Draft    => 'Draft',
            self::Sent     => 'Dikirim',
            self::Approved => 'Disetujui',
            self::Rejected => 'Ditolak',
            self::Expired  => 'Expired',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Draft    => 'gray',
            self::Sent     => 'blue',
            self::Approved => 'green',
            self::Rejected => 'red',
            self::Expired  => 'orange',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::Draft    => 'bg-gray-100 text-gray-700',
            self::Sent     => 'bg-blue-100 text-blue-700',
            self::Approved => 'bg-green-100 text-green-700',
            self::Rejected => 'bg-red-100 text-red-700',
            self::Expired  => 'bg-orange-100 text-orange-700',
        };
    }
}
