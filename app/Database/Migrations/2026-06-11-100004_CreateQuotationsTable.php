<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateQuotationsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => false],
            'client_id' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => false],
            'number' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => false],
            'date' => ['type' => 'DATE', 'null' => false],
            'valid_until' => ['type' => 'DATE', 'null' => true],
            'subtotal' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'null' => false, 'default' => '0.00'],
            'discount_type' => ['type' => 'ENUM', 'constraint' => ['percent', 'nominal'], 'null' => true],
            'discount_value' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'null' => false, 'default' => '0.00'],
            'tax_rate' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'null' => false, 'default' => '0.00'],
            'tax_amount' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'null' => false, 'default' => '0.00'],
            'total' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'null' => false, 'default' => '0.00'],
            'terms' => ['type' => 'TEXT', 'null' => true],
            'notes' => ['type' => 'TEXT', 'null' => true],
            'status' => ['type' => 'ENUM', 'constraint' => ['draft', 'sent', 'approved', 'rejected', 'expired'], 'null' => false, 'default' => 'draft'],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey(['number', 'user_id'], 'quotations_number_user_id_unique');
        $this->forge->addKey(['user_id', 'status'], false, false, 'quotations_user_id_status_index');
        $this->forge->addKey('client_id', false, false, 'quotations_client_id_index');
        $this->forge->addForeignKey('client_id', 'clients', 'id', '', '', 'quotations_client_id_foreign');
        $this->forge->addForeignKey('user_id', 'users', 'id', '', 'CASCADE', 'quotations_user_id_foreign');
        $this->forge->createTable('quotations', true, ['ENGINE' => 'InnoDB']);

        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'quotation_id' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => false],
            'product_id' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false],
            'description' => ['type' => 'TEXT', 'null' => true],
            'qty' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => false],
            'unit' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => false, 'default' => 'pcs'],
            'unit_price' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'null' => false],
            'discount_type' => ['type' => 'ENUM', 'constraint' => ['percent', 'nominal'], 'null' => true],
            'discount_value' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'null' => false, 'default' => '0.00'],
            'subtotal' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'null' => false],
            'sort_order' => ['type' => 'INT', 'unsigned' => true, 'null' => false, 'default' => 0],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('quotation_id', false, false, 'quotation_items_quotation_id_index');
        $this->forge->addForeignKey('product_id', 'products', 'id', '', 'SET NULL', 'quotation_items_product_id_foreign');
        $this->forge->addForeignKey('quotation_id', 'quotations', 'id', '', 'CASCADE', 'quotation_items_quotation_id_foreign');
        $this->forge->createTable('quotation_items', true, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('quotation_items', true);
        $this->forge->dropTable('quotations', true);
    }
}
