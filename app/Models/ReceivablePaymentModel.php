<?php

namespace App\Models;

use App\Entities\ReceivablePayment;
use CodeIgniter\Model;

class ReceivablePaymentModel extends Model
{
    protected $table         = 'receivable_payments';
    protected $returnType    = ReceivablePayment::class;
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $allowedFields = ['receivable_id', 'transaction_id', 'amount', 'payment_date', 'notes'];
}
