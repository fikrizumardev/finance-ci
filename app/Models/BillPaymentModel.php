<?php

namespace App\Models;

use App\Entities\BillPayment;

class BillPaymentModel extends UserScopedModel
{
    protected $table         = 'bill_payments';
    protected $returnType    = BillPayment::class;
    protected $allowedFields = [
        'user_id', 'utility_bill_id', 'period_month', 'period_year', 'payment_date',
        'amount_paid', 'payment_method', 'reference_number', 'screenshot_path', 'notes', 'transaction_id',
    ];

    /**
     * @return $this
     */
    public function forPeriod(int $month, int $year): static
    {
        return $this->where('period_month', $month)->where('period_year', $year);
    }

    /**
     * Pembayaran sebuah tagihan untuk periode tertentu (UtilityBill::paymentForPeriod di Laravel).
     */
    public function findForBillPeriod(int $billId, int $month, int $year): ?BillPayment
    {
        return $this->where('utility_bill_id', $billId)->forPeriod($month, $year)->first();
    }
}
