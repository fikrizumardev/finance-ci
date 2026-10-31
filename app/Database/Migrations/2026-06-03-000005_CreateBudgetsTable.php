<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBudgetsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => false],
            'category_id' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => false],
            'amount' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'null' => false],
            'period' => ['type' => 'ENUM', 'constraint' => ['monthly', 'yearly'], 'null' => false],
            'start_date' => ['type' => 'DATE', 'null' => false],
            'end_date' => ['type' => 'DATE', 'null' => true],
            'alert_percentage' => ['type' => 'TINYINT', 'unsigned' => true, 'null' => false, 'default' => 80],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('user_id', false, false, 'budgets_user_id_index');
        $this->forge->addKey('category_id', false, false, 'budgets_category_id_index');
        $this->forge->addKey(['user_id', 'period'], false, false, 'budgets_user_id_period_index');
        $this->forge->addForeignKey('category_id', 'categories', 'id', '', 'CASCADE', 'budgets_category_id_foreign');
        $this->forge->addForeignKey('user_id', 'users', 'id', '', 'CASCADE', 'budgets_user_id_foreign');
        $this->forge->createTable('budgets', true, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('budgets', true);
    }
}
