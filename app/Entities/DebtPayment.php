<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;
use CodeIgniter\I18n\Time;

/**
 * @property Time $payment_date
 */
class DebtPayment extends Entity
{
    protected $casts = [
        'id'           => 'integer',
        'debt_id'      => 'integer',
        'payment_date' => 'datetime',
    ];
}
