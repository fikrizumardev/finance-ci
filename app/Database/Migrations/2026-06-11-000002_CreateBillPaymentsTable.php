<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBillPaymentsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => false],
            'utility_bill_id' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => false],
            'period_month' => ['type' => 'TINYINT', 'null' => false, 'comment' => '1-12'],
            'period_year' => ['type' => 'SMALLINT', 'unsigned' => true, 'null' => false],
            'payment_date' => ['type' => 'DATE', 'null' => false],
            'amount_paid' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'null' => false],
            'payment_method' => ['type' => 'ENUM', 'constraint' => ['transfer', 'autodebet', 'cash', 'virtual_account', 'qris', 'other'], 'null' => false, 'default' => 'transfer'],
            'reference_number' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'comment' => 'No. referensi/struk'],
            'screenshot_path' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'comment' => 'Path screenshot bukti bayar'],
            'notes' => ['type' => 'TEXT', 'null' => true],
            'transaction_id' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey(['utility_bill_id', 'period_month', 'period_year'], 'uq_bill_period');
        $this->forge->addKey('user_id', false, false, 'bill_payments_user_id_index');
        $this->forge->addKey(['period_year', 'period_month'], false, false, 'bill_payments_period_year_period_month_index');
        $this->forge->addForeignKey('transaction_id', 'transactions', 'id', '', 'SET NULL', 'bill_payments_transaction_id_foreign');
        $this->forge->addForeignKey('user_id', 'users', 'id', '', 'CASCADE', 'bill_payments_user_id_foreign');
        $this->forge->addForeignKey('utility_bill_id', 'utility_bills', 'id', '', 'CASCADE', 'bill_payments_utility_bill_id_foreign');
        $this->forge->createTable('bill_payments', true, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('bill_payments', true);
    }
}
