<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateReceiptsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => false],
            'invoice_payment_id' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
            'client_id' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
            'number' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => false],
            'date' => ['type' => 'DATE', 'null' => false],
            'amount' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'null' => false],
            'amount_in_words' => ['type' => 'TEXT', 'null' => true],
            'payment_for' => ['type' => 'TEXT', 'null' => true],
            'payment_method' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'received_by' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'notes' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey(['number', 'user_id'], 'receipts_number_user_id_unique');
        $this->forge->addKey('user_id', false, false, 'receipts_user_id_index');
        $this->forge->addKey('client_id', false, false, 'receipts_client_id_index');
        $this->forge->addForeignKey('client_id', 'clients', 'id', '', 'SET NULL', 'receipts_client_id_foreign');
        $this->forge->addForeignKey('invoice_payment_id', 'invoice_payments', 'id', '', 'SET NULL', 'receipts_invoice_payment_id_foreign');
        $this->forge->addForeignKey('user_id', 'users', 'id', '', 'CASCADE', 'receipts_user_id_foreign');
        $this->forge->createTable('receipts', true, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('receipts', true);
    }
}
