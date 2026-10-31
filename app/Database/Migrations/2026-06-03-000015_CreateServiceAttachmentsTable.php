<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateServiceAttachmentsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'service_history_id' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => false],
            'file_name' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false],
            'file_path' => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => false],
            'file_size' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'mime_type' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('service_history_id', false, false, 'service_attachments_service_history_id_index');
        $this->forge->addForeignKey('service_history_id', 'service_histories', 'id', '', 'CASCADE', 'service_attachments_service_history_id_foreign');
        $this->forge->createTable('service_attachments', true, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('service_attachments', true);
    }
}
