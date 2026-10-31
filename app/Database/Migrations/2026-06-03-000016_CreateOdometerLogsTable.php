<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOdometerLogsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'vehicle_id' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => false],
            'log_date' => ['type' => 'DATE', 'null' => false],
            'odometer' => ['type' => 'INT', 'unsigned' => true, 'null' => false],
            'notes' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('vehicle_id', false, false, 'odometer_logs_vehicle_id_index');
        $this->forge->addKey('log_date', false, false, 'odometer_logs_log_date_index');
        $this->forge->addKey(['vehicle_id', 'log_date'], false, false, 'odometer_logs_vehicle_id_log_date_index');
        $this->forge->addForeignKey('vehicle_id', 'vehicles', 'id', '', 'CASCADE', 'odometer_logs_vehicle_id_foreign');
        $this->forge->createTable('odometer_logs', true, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('odometer_logs', true);
    }
}
