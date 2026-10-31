<?php

namespace App\Models;

use App\Entities\Debt;

class DebtModel extends UserScopedModel
{
    protected $table         = 'debts';
    protected $returnType    = Debt::class;
    protected $allowedFields = [
        'user_id', 'contact_id', 'amount', 'remaining_amount', 'interest_rate',
        'borrowed_date', 'due_date', 'status', 'purpose', 'notes',
    ];
}
