<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUtilityBillsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => false],
            'name' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false],
            'category' => ['type' => 'ENUM', 'constraint' => ['wifi', 'electricity', 'water', 'gas', 'internet', 'phone', 'tv', 'other'], 'null' => false, 'default' => 'other'],
            'provider_name' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'customer_number' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'comment' => 'Nomor pelanggan/ID'],
            'estimated_amount' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'null' => true, 'comment' => 'Estimasi tagihan per bulan'],
            'due_day' => ['type' => 'TINYINT', 'null' => true, 'comment' => 'Tanggal jatuh tempo (1-31)'],
            'is_active' => ['type' => 'TINYINT', 'constraint' => 1, 'null' => false, 'default' => 1],
            'notes' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey(['user_id', 'is_active'], false, false, 'utility_bills_user_id_is_active_index');
        $this->forge->addKey('category', false, false, 'utility_bills_category_index');
        $this->forge->addForeignKey('user_id', 'users', 'id', '', 'CASCADE', 'utility_bills_user_id_foreign');
        $this->forge->createTable('utility_bills', true, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('utility_bills', true);
    }
}
