<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBusinessSettingsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => false],
            'business_name' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'logo_path' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'address' => ['type' => 'TEXT', 'null' => true],
            'phone' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'email' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'website' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'npwp' => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'bank_name' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'bank_account' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'bank_account_name' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'default_terms' => ['type' => 'TEXT', 'null' => true],
            'default_notes' => ['type' => 'TEXT', 'null' => true],
            'quotation_prefix' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => false, 'default' => 'QUO'],
            'invoice_prefix' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => false, 'default' => 'INV'],
            'receipt_prefix' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => false, 'default' => 'RCP'],
            'quotation_counter' => ['type' => 'INT', 'unsigned' => true, 'null' => false, 'default' => 0],
            'invoice_counter' => ['type' => 'INT', 'unsigned' => true, 'null' => false, 'default' => 0],
            'receipt_counter' => ['type' => 'INT', 'unsigned' => true, 'null' => false, 'default' => 0],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('user_id', 'business_settings_user_id_unique');
        $this->forge->addForeignKey('user_id', 'users', 'id', '', 'CASCADE', 'business_settings_user_id_foreign');
        $this->forge->createTable('business_settings', true, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('business_settings', true);
    }
}
