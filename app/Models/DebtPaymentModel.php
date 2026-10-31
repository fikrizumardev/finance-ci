<?php

namespace App\Models;

use App\Entities\DebtPayment;
use CodeIgniter\Model;

class DebtPaymentModel extends Model
{
    protected $table         = 'debt_payments';
    protected $returnType    = DebtPayment::class;
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $allowedFields = ['debt_id', 'transaction_id', 'amount', 'payment_date', 'notes'];
}
