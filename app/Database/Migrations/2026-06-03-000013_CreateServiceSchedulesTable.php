<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateServiceSchedulesTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'vehicle_id' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => false],
            'service_type' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => false, 'comment' => 'Oil Change, Tune Up, Brake Pad, etc'],
            'interval_km' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'interval_months' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'last_service_km' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'last_service_date' => ['type' => 'DATE', 'null' => true],
            'next_service_km' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'next_service_date' => ['type' => 'DATE', 'null' => true],
            'reminder_before_km' => ['type' => 'INT', 'unsigned' => true, 'null' => false, 'default' => 100],
            'reminder_before_days' => ['type' => 'INT', 'unsigned' => true, 'null' => false, 'default' => 7],
            'is_active' => ['type' => 'TINYINT', 'constraint' => 1, 'null' => false, 'default' => 1],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('vehicle_id', false, false, 'service_schedules_vehicle_id_index');
        $this->forge->addKey('next_service_date', false, false, 'service_schedules_next_service_date_index');
        $this->forge->addKey(['vehicle_id', 'is_active'], false, false, 'service_schedules_vehicle_id_is_active_index');
        $this->forge->addForeignKey('vehicle_id', 'vehicles', 'id', '', 'CASCADE', 'service_schedules_vehicle_id_foreign');
        $this->forge->createTable('service_schedules', true, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('service_schedules', true);
    }
}
