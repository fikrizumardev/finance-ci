<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFuelLogsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'vehicle_id' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => false],
            'transaction_id' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
            'fill_date' => ['type' => 'DATE', 'null' => false],
            'odometer' => ['type' => 'INT', 'unsigned' => true, 'null' => false],
            'fuel_type' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => false, 'comment' => 'Pertalite, Pertamax, Pertamax Turbo'],
            'liters' => ['type' => 'DECIMAL', 'constraint' => '8,2', 'null' => false],
            'price_per_liter' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => false],
            'total_cost' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'null' => false],
            'fuel_station' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'is_full_tank' => ['type' => 'TINYINT', 'constraint' => 1, 'null' => false, 'default' => 1],
            'notes' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('vehicle_id', false, false, 'fuel_logs_vehicle_id_index');
        $this->forge->addKey('transaction_id', false, false, 'fuel_logs_transaction_id_index');
        $this->forge->addKey('fill_date', false, false, 'fuel_logs_fill_date_index');
        $this->forge->addKey(['vehicle_id', 'fill_date'], false, false, 'fuel_logs_vehicle_id_fill_date_index');
        $this->forge->addForeignKey('transaction_id', 'transactions', 'id', '', 'SET NULL', 'fuel_logs_transaction_id_foreign');
        $this->forge->addForeignKey('vehicle_id', 'vehicles', 'id', '', 'CASCADE', 'fuel_logs_vehicle_id_foreign');
        $this->forge->createTable('fuel_logs', true, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('fuel_logs', true);
    }
}
