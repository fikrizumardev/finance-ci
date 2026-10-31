<?php

namespace App\Models;

use App\Entities\Contact;

class ContactModel extends UserScopedModel
{
    protected $table         = 'contacts';
    protected $returnType    = Contact::class;
    protected $allowedFields = ['user_id', 'name', 'phone', 'email', 'address', 'notes'];
}
