<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateReceivablePaymentsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'receivable_id' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => false],
            'transaction_id' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
            'amount' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'null' => false],
            'payment_date' => ['type' => 'DATE', 'null' => false],
            'notes' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('receivable_id', false, false, 'receivable_payments_receivable_id_index');
        $this->forge->addKey('transaction_id', false, false, 'receivable_payments_transaction_id_index');
        $this->forge->addKey('payment_date', false, false, 'receivable_payments_payment_date_index');
        $this->forge->addForeignKey('receivable_id', 'receivables', 'id', '', 'CASCADE', 'receivable_payments_receivable_id_foreign');
        $this->forge->addForeignKey('transaction_id', 'transactions', 'id', '', 'SET NULL', 'receivable_payments_transaction_id_foreign');
        $this->forge->createTable('receivable_payments', true, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('receivable_payments', true);
    }
}
