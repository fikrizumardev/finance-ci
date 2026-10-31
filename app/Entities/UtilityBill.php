<?php

namespace App\Entities;

use App\Enums\BillCategory;
use CodeIgniter\Entity\Entity;
use CodeIgniter\I18n\Time;

/**
 * @property BillCategory $category
 */
class UtilityBill extends Entity
{
    protected $casts = [
        'id'        => 'integer',
        'user_id'   => 'integer',
        'category'  => 'enum[' . BillCategory::class . ']',
        'due_day'   => '?integer',
        'is_active' => 'boolean',
    ];

    /**
     * Cek apakah mendekati jatuh tempo (dalam X hari ke depan) pada bulan berjalan.
     */
    public function isDueSoon(int $daysAhead = 3, ?Time $today = null): bool
    {
        if (! $this->attributes['due_day']) {
            return false;
        }

        $today ??= Time::today();
        $daysLeft = (int) $this->attributes['due_day'] - $today->getDay();

        return $daysLeft >= 0 && $daysLeft <= $daysAhead;
    }
}
