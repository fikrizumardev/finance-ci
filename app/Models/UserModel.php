<?php

namespace App\Models;

use App\Entities\User;
use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table         = 'users';
    protected $returnType    = User::class;
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $allowedFields = ['name', 'email', 'email_verified_at', 'password', 'remember_token'];

    public function findByEmail(string $email): ?User
    {
        return $this->where('email', $email)->first();
    }
}
