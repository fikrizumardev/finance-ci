<?php

namespace App\Database\Seeds;

use App\Enums\CategoryType;
use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $income  = CategoryType::INCOME->value;
        $expense = CategoryType::EXPENSE->value;

        $categories = [
            // Income Categories
            ['name' => 'Gaji', 'type' => $income, 'color' => '#10b981', 'icon' => 'briefcase'],
            ['name' => 'Bonus', 'type' => $income, 'color' => '#059669', 'icon' => 'gift'],
            ['name' => 'Investasi', 'type' => $income, 'color' => '#047857', 'icon' => 'trending-up'],
            ['name' => 'Hadiah', 'type' => $income, 'color' => '#34d399', 'icon' => 'gift'],
            ['name' => 'Lain-lain', 'type' => $income, 'color' => '#6ee7b7', 'icon' => 'more-horizontal'],

            // Expense Categories
            ['name' => 'Makanan & Minuman', 'type' => $expense, 'color' => '#ef4444', 'icon' => 'utensils'],
            ['name' => 'Transportasi', 'type' => $expense, 'color' => '#f59e0b', 'icon' => 'car'],
            ['name' => 'Belanja', 'type' => $expense, 'color' => '#ec4899', 'icon' => 'shopping-cart'],
            ['name' => 'Tagihan', 'type' => $expense, 'color' => '#8b5cf6', 'icon' => 'file-text'],
            ['name' => 'Hiburan', 'type' => $expense, 'color' => '#06b6d4', 'icon' => 'film'],
            ['name' => 'Kesehatan', 'type' => $expense, 'color' => '#f43f5e', 'icon' => 'heart'],
            ['name' => 'Pendidikan', 'type' => $expense, 'color' => '#3b82f6', 'icon' => 'book'],
            ['name' => 'Rumah Tangga', 'type' => $expense, 'color' => '#84cc16', 'icon' => 'home'],
            ['name' => 'Pakaian', 'type' => $expense, 'color' => '#a855f7', 'icon' => 'shirt'],
            ['name' => 'Komunikasi', 'type' => $expense, 'color' => '#14b8a6', 'icon' => 'phone'],
            ['name' => 'Servis Motor', 'type' => $expense, 'color' => '#f97316', 'icon' => 'wrench'],
            ['name' => 'BBM Motor', 'type' => $expense, 'color' => '#f59e0b', 'icon' => 'droplet'],
            ['name' => 'Cicilan/Hutang', 'type' => $expense, 'color' => '#dc2626', 'icon' => 'credit-card'],
            ['name' => 'Lain-lain', 'type' => $expense, 'color' => '#64748b', 'icon' => 'more-horizontal'],
        ];

        $now     = Time::now()->toDateTimeString();
        $builder = $this->db->table('categories');

        foreach ($categories as $category) {
            $exists = $builder->where('user_id', null)
                ->where('name', $category['name'])
                ->where('type', $category['type'])
                ->countAllResults() > 0;

            if ($exists) {
                continue;
            }

            $builder->insert($category + [
                'is_system'  => 1,
                'is_active'  => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
