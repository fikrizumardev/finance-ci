<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateVehiclesTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => false],
            'brand' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => false, 'comment' => 'Honda, Yamaha, Suzuki, etc'],
            'model' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => false],
            'year' => ['type' => 'YEAR', 'null' => false],
            'license_plate' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => false],
            'chassis_number' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'engine_number' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'color' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'photo_path' => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'purchase_date' => ['type' => 'DATE', 'null' => true],
            'purchase_price' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'null' => true],
            'initial_odometer' => ['type' => 'INT', 'unsigned' => true, 'null' => false, 'default' => 0],
            'current_odometer' => ['type' => 'INT', 'unsigned' => true, 'null' => false, 'default' => 0],
            'is_active' => ['type' => 'TINYINT', 'constraint' => 1, 'null' => false, 'default' => 1],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('user_id', false, false, 'vehicles_user_id_index');
        $this->forge->addKey('license_plate', false, false, 'vehicles_license_plate_index');
        $this->forge->addKey(['user_id', 'is_active'], false, false, 'vehicles_user_id_is_active_index');
        $this->forge->addForeignKey('user_id', 'users', 'id', '', 'CASCADE', 'vehicles_user_id_foreign');
        $this->forge->createTable('vehicles', true, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('vehicles', true);
    }
}
