<?php

namespace App\Entities;

use App\Enums\PaymentMethod;
use CodeIgniter\Entity\Entity;
use CodeIgniter\I18n\Time;

/**
 * @property PaymentMethod $payment_method
 * @property Time          $payment_date
 * @property string        $period_label
 */
class BillPayment extends Entity
{
    protected $casts = [
        'id'              => 'integer',
        'user_id'         => 'integer',
        'utility_bill_id' => 'integer',
        'period_month'    => 'integer',
        'period_year'     => 'integer',
        'payment_date'    => 'datetime',
        'payment_method'  => 'enum[' . PaymentMethod::class . ']',
    ];

    /**
     * Nama periode yang mudah dibaca (mis: "Juni 2026"), diakses sebagai $payment->period_label.
     */
    public function getPeriodLabel(): string
    {
        $month = (int) $this->attributes['period_month'];

        return (month_name($month) ?: $month) . ' ' . $this->attributes['period_year'];
    }

    public function hasScreenshot(): bool
    {
        return ! empty($this->attributes['screenshot_path']);
    }
}
