<?php

namespace App\Enums;

enum BillCategory: string
{
    case Wifi = 'wifi';
    case Electricity = 'electricity';
    case Water = 'water';
    case Gas = 'gas';
    case Internet = 'internet';
    case Phone = 'phone';
    case Tv = 'tv';
    case Other = 'other';

    public function label(): string
    {
        return match($this) {
            self::Wifi        => 'WiFi',
            self::Electricity => 'Listrik PLN',
            self::Water       => 'Air PDAM',
            self::Gas         => 'Gas PGN',
            self::Internet    => 'Internet',
            self::Phone       => 'Telepon',
            self::Tv          => 'TV Kabel',
            self::Other       => 'Lainnya',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::Wifi        => '📶',
            self::Electricity => '⚡',
            self::Water       => '💧',
            self::Gas         => '🔥',
            self::Internet    => '🌐',
            self::Phone       => '📞',
            self::Tv          => '📺',
            self::Other       => '🏠',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Wifi        => 'blue',
            self::Electricity => 'yellow',
            self::Water       => 'cyan',
            self::Gas         => 'orange',
            self::Internet    => 'purple',
            self::Phone       => 'green',
            self::Tv          => 'pink',
            self::Other       => 'zinc',
        };
    }
}
