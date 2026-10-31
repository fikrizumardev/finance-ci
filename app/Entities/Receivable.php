<?php

namespace App\Entities;

use App\Enums\DebtStatus;
use CodeIgniter\Entity\Entity;
use CodeIgniter\I18n\Time;

/**
 * @property DebtStatus $status
 * @property Time       $lent_date
 * @property Time|null  $due_date
 */
class Receivable extends Entity
{
    protected $casts = [
        'id'         => 'integer',
        'user_id'    => 'integer',
        'contact_id' => 'integer',
        'lent_date'  => 'datetime',
        'due_date'   => '?datetime',
        'status'     => 'enum[' . DebtStatus::class . ']',
    ];
}
