<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCategoriesTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
            'parent_id' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false],
            'type' => ['type' => 'ENUM', 'constraint' => ['income', 'expense'], 'null' => false],
            'color' => ['type' => 'VARCHAR', 'constraint' => 7, 'null' => true],
            'icon' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'is_system' => ['type' => 'TINYINT', 'constraint' => 1, 'null' => false, 'default' => 0],
            'is_active' => ['type' => 'TINYINT', 'constraint' => 1, 'null' => false, 'default' => 1],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('user_id', false, false, 'categories_user_id_index');
        $this->forge->addKey('type', false, false, 'categories_type_index');
        $this->forge->addKey('parent_id', false, false, 'categories_parent_id_index');
        $this->forge->addKey(['user_id', 'type', 'is_active'], false, false, 'categories_user_id_type_is_active_index');
        $this->forge->addForeignKey('parent_id', 'categories', 'id', '', 'SET NULL', 'categories_parent_id_foreign');
        $this->forge->addForeignKey('user_id', 'users', 'id', '', 'CASCADE', 'categories_user_id_foreign');
        $this->forge->createTable('categories', true, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('categories', true);
    }
}
