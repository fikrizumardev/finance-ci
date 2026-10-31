<?php

namespace App\Entities;

use App\Enums\DebtStatus;
use CodeIgniter\Entity\Entity;
use CodeIgniter\I18n\Time;

/**
 * @property DebtStatus $status
 * @property Time       $borrowed_date
 * @property Time|null  $due_date
 */
class Debt extends Entity
{
    protected $casts = [
        'id'            => 'integer',
        'user_id'       => 'integer',
        'contact_id'    => 'integer',
        'borrowed_date' => 'datetime',
        'due_date'      => '?datetime',
        'status'        => 'enum[' . DebtStatus::class . ']',
    ];
}
