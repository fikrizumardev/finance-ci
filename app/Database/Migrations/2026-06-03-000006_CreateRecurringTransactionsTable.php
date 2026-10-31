<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRecurringTransactionsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => false],
            'account_id' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => false],
            'category_id' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => false],
            'type' => ['type' => 'ENUM', 'constraint' => ['income', 'expense'], 'null' => false],
            'amount' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'null' => false],
            'description' => ['type' => 'TEXT', 'null' => true],
            'frequency' => ['type' => 'ENUM', 'constraint' => ['daily', 'weekly', 'monthly', 'yearly'], 'null' => false],
            'start_date' => ['type' => 'DATE', 'null' => false],
            'end_date' => ['type' => 'DATE', 'null' => true],
            'last_generated_at' => ['type' => 'DATE', 'null' => true],
            'next_generation_date' => ['type' => 'DATE', 'null' => true],
            'is_active' => ['type' => 'TINYINT', 'constraint' => 1, 'null' => false, 'default' => 1],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('user_id', false, false, 'recurring_transactions_user_id_index');
        $this->forge->addKey('account_id', false, false, 'recurring_transactions_account_id_index');
        $this->forge->addKey('category_id', false, false, 'recurring_transactions_category_id_index');
        $this->forge->addKey(['is_active', 'next_generation_date'], false, false, 'recurring_transactions_is_active_next_generation_date_index');
        $this->forge->addForeignKey('account_id', 'accounts', 'id', '', '', 'recurring_transactions_account_id_foreign');
        $this->forge->addForeignKey('category_id', 'categories', 'id', '', '', 'recurring_transactions_category_id_foreign');
        $this->forge->addForeignKey('user_id', 'users', 'id', '', 'CASCADE', 'recurring_transactions_user_id_foreign');
        $this->forge->createTable('recurring_transactions', true, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('recurring_transactions', true);
    }
}
