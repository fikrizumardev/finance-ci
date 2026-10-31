<?php

namespace App\Models;

use App\Entities\UtilityBill;

class UtilityBillModel extends UserScopedModel
{
    protected $table         = 'utility_bills';
    protected $returnType    = UtilityBill::class;
    protected $allowedFields = [
        'user_id', 'name', 'category', 'provider_name', 'customer_number',
        'estimated_amount', 'due_day', 'is_active', 'notes',
    ];

    /**
     * @return $this
     */
    public function active(): static
    {
        return $this->where($this->table . '.is_active', 1);
    }
}
