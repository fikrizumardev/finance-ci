<?php

namespace App\Models;

use App\Entities\Receivable;

class ReceivableModel extends UserScopedModel
{
    protected $table         = 'receivables';
    protected $returnType    = Receivable::class;
    protected $allowedFields = [
        'user_id', 'contact_id', 'amount', 'remaining_amount', 'interest_rate',
        'lent_date', 'due_date', 'status', 'purpose', 'notes',
    ];
}
