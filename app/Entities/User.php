<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class User extends Entity
{
    protected $dates = ['email_verified_at', 'created_at', 'updated_at'];

    /**
     * Inisial dari huruf pertama setiap kata pada nama (sama dengan User::initials() di Laravel).
     */
    public function initials(): string
    {
        return implode('', array_map(
            static fn (string $part): string => mb_substr($part, 0, 1),
            explode(' ', (string) $this->attributes['name']),
        ));
    }
}
