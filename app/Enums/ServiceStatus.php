<?php

namespace App\Enums;

enum ServiceStatus: string
{
    case COMPLETED = 'completed';
    case IN_PROGRESS = 'in_progress';
    case PENDING = 'pending';

    public function label(): string
    {
        return match ($this) {
            self::COMPLETED => 'Selesai',
            self::IN_PROGRESS => 'Dalam Proses',
            self::PENDING => 'Pending',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::COMPLETED => 'green',
            self::IN_PROGRESS => 'blue',
            self::PENDING => 'yellow',
        };
    }
}
