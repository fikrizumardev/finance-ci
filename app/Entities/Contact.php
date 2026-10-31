<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Contact extends Entity
{
    protected $casts = [
        'id'      => 'integer',
        'user_id' => 'integer',
    ];
}
