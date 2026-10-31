<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $now   = Time::now()->toDateTimeString();
        $users = [
            ['name' => 'Demo User', 'email' => 'demo@myfinance.app'],
            ['name' => 'Admin', 'email' => 'admin@myfinance.app'],
        ];

        $builder = $this->db->table('users');

        foreach ($users as $data) {
            if ($builder->where('email', $data['email'])->countAllResults() > 0) {
                continue;
            }

            $builder->insert($data + [
                'password'          => password_hash('password', PASSWORD_BCRYPT),
                'email_verified_at' => $now,
                'created_at'        => $now,
                'updated_at'        => $now,
            ]);
        }

        if (is_cli() && ENVIRONMENT !== 'testing') {
            echo "\n  Users seeded:\n";
            echo "  -------------------------------------\n";
            echo "  demo@myfinance.app  ->  password: password\n";
            echo "  admin@myfinance.app ->  password: password\n";
            echo "  -------------------------------------\n";
        }
    }
}
