<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateReceivablesTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => false],
            'contact_id' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => false],
            'amount' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'null' => false],
            'remaining_amount' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'null' => false],
            'interest_rate' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'null' => true],
            'lent_date' => ['type' => 'DATE', 'null' => false],
            'due_date' => ['type' => 'DATE', 'null' => true],
            'status' => ['type' => 'ENUM', 'constraint' => ['unpaid', 'partial', 'paid'], 'null' => false, 'default' => 'unpaid'],
            'purpose' => ['type' => 'TEXT', 'null' => true],
            'notes' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('user_id', false, false, 'receivables_user_id_index');
        $this->forge->addKey('contact_id', false, false, 'receivables_contact_id_index');
        $this->forge->addKey('status', false, false, 'receivables_status_index');
        $this->forge->addKey('due_date', false, false, 'receivables_due_date_index');
        $this->forge->addKey(['user_id', 'status'], false, false, 'receivables_user_id_status_index');
        $this->forge->addForeignKey('contact_id', 'contacts', 'id', '', '', 'receivables_contact_id_foreign');
        $this->forge->addForeignKey('user_id', 'users', 'id', '', 'CASCADE', 'receivables_user_id_foreign');
        $this->forge->createTable('receivables', true, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('receivables', true);
    }
}
