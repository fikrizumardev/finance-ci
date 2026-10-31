<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTransactionsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => false],
            'account_id' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => false],
            'category_id' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => false],
            'type' => ['type' => 'ENUM', 'constraint' => ['income', 'expense', 'transfer'], 'null' => false],
            'amount' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'null' => false],
            'transaction_date' => ['type' => 'DATE', 'null' => false],
            'description' => ['type' => 'TEXT', 'null' => true],
            'location' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'notes' => ['type' => 'TEXT', 'null' => true],
            'reference_type' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true, 'comment' => 'debt_payment, receivable_payment, service_history, fuel_log'],
            'reference_id' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'deleted_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('user_id', false, false, 'transactions_user_id_index');
        $this->forge->addKey('account_id', false, false, 'transactions_account_id_index');
        $this->forge->addKey('category_id', false, false, 'transactions_category_id_index');
        $this->forge->addKey('transaction_date', false, false, 'transactions_transaction_date_index');
        $this->forge->addKey('type', false, false, 'transactions_type_index');
        $this->forge->addKey(['user_id', 'transaction_date'], false, false, 'transactions_user_id_transaction_date_index');
        $this->forge->addKey(['reference_type', 'reference_id'], false, false, 'transactions_reference_type_reference_id_index');
        $this->forge->addForeignKey('account_id', 'accounts', 'id', '', '', 'transactions_account_id_foreign');
        $this->forge->addForeignKey('category_id', 'categories', 'id', '', '', 'transactions_category_id_foreign');
        $this->forge->addForeignKey('user_id', 'users', 'id', '', 'CASCADE', 'transactions_user_id_foreign');
        $this->forge->createTable('transactions', true, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('transactions', true);
    }
}
