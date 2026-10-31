<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAccountsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => false],
            'name' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false],
            'type' => ['type' => 'ENUM', 'constraint' => ['cash', 'bank', 'e-wallet', 'investment', 'other'], 'null' => false],
            'initial_balance' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'null' => false, 'default' => '0.00'],
            'current_balance' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'null' => false, 'default' => '0.00'],
            'currency' => ['type' => 'VARCHAR', 'constraint' => 3, 'null' => false, 'default' => 'IDR'],
            'color' => ['type' => 'VARCHAR', 'constraint' => 7, 'null' => true],
            'icon' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'description' => ['type' => 'TEXT', 'null' => true],
            'is_active' => ['type' => 'TINYINT', 'constraint' => 1, 'null' => false, 'default' => 1],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'deleted_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('user_id', false, false, 'accounts_user_id_index');
        $this->forge->addKey('type', false, false, 'accounts_type_index');
        $this->forge->addKey(['user_id', 'is_active'], false, false, 'accounts_user_id_is_active_index');
        $this->forge->addForeignKey('user_id', 'users', 'id', '', 'CASCADE', 'accounts_user_id_foreign');
        $this->forge->createTable('accounts', true, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('accounts', true);
    }
}
