<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateServiceHistoriesTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'vehicle_id' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => false],
            'transaction_id' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
            'service_date' => ['type' => 'DATE', 'null' => false],
            'odometer' => ['type' => 'INT', 'unsigned' => true, 'null' => false],
            'service_type' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => false],
            'workshop_name' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'workshop_address' => ['type' => 'TEXT', 'null' => true],
            'cost' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'null' => false],
            'parts_replaced' => ['type' => 'TEXT', 'null' => true],
            'mechanic_notes' => ['type' => 'TEXT', 'null' => true],
            'status' => ['type' => 'ENUM', 'constraint' => ['completed', 'in_progress', 'pending'], 'null' => false, 'default' => 'completed'],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('vehicle_id', false, false, 'service_histories_vehicle_id_index');
        $this->forge->addKey('transaction_id', false, false, 'service_histories_transaction_id_index');
        $this->forge->addKey('service_date', false, false, 'service_histories_service_date_index');
        $this->forge->addKey(['vehicle_id', 'service_date'], false, false, 'service_histories_vehicle_id_service_date_index');
        $this->forge->addForeignKey('transaction_id', 'transactions', 'id', '', 'SET NULL', 'service_histories_transaction_id_foreign');
        $this->forge->addForeignKey('vehicle_id', 'vehicles', 'id', '', 'CASCADE', 'service_histories_vehicle_id_foreign');
        $this->forge->createTable('service_histories', true, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('service_histories', true);
    }
}
