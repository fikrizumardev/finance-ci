<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class UtilityBillSeeder extends Seeder
{
    public function run(): void
    {
        $user = $this->db->table('users')->orderBy('id')->get(1)->getRow();

        if (! $user) {
            return;
        }

        $bills = [
            [
                'name'             => 'Listrik PLN',
                'category'         => 'electricity',
                'provider_name'    => 'PLN',
                'estimated_amount' => 350000,
                'due_day'          => 20,
            ],
            [
                'name'             => 'Air PDAM',
                'category'         => 'water',
                'provider_name'    => 'PDAM',
                'estimated_amount' => 120000,
                'due_day'          => 15,
            ],
            [
                'name'             => 'WiFi IndiHome',
                'category'         => 'wifi',
                'provider_name'    => 'Telkom Indonesia',
                'estimated_amount' => 375000,
                'due_day'          => 25,
            ],
            [
                'name'             => 'Gas PGN',
                'category'         => 'gas',
                'provider_name'    => 'PGN',
                'estimated_amount' => 150000,
                'due_day'          => 10,
            ],
        ];

        $now     = Time::now()->toDateTimeString();
        $builder = $this->db->table('utility_bills');

        foreach ($bills as $bill) {
            $exists = $builder->where('user_id', $user->id)
                ->where('name', $bill['name'])
                ->countAllResults() > 0;

            if ($exists) {
                continue;
            }

            $builder->insert($bill + [
                'user_id'    => $user->id,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
