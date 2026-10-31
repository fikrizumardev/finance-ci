<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInvoicesTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => false],
            'client_id' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => false],
            'quotation_id' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
            'number' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => false],
            'date' => ['type' => 'DATE', 'null' => false],
            'due_date' => ['type' => 'DATE', 'null' => true],
            'subtotal' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'null' => false, 'default' => '0.00'],
            'discount_type' => ['type' => 'ENUM', 'constraint' => ['percent', 'nominal'], 'null' => true],
            'discount_value' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'null' => false, 'default' => '0.00'],
            'tax_rate' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'null' => false, 'default' => '0.00'],
            'tax_amount' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'null' => false, 'default' => '0.00'],
            'total' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'null' => false, 'default' => '0.00'],
            'paid_amount' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'null' => false, 'default' => '0.00'],
            'remaining_amount' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'null' => false, 'default' => '0.00'],
            'bank_info' => ['type' => 'TEXT', 'null' => true],
            'terms' => ['type' => 'TEXT', 'null' => true],
            'notes' => ['type' => 'TEXT', 'null' => true],
            'status' => ['type' => 'ENUM', 'constraint' => ['draft', 'issued', 'partial', 'paid', 'overdue', 'cancelled'], 'null' => false, 'default' => 'draft'],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey(['number', 'user_id'], 'invoices_number_user_id_unique');
        $this->forge->addKey(['user_id', 'status'], false, false, 'invoices_user_id_status_index');
        $this->forge->addKey('client_id', false, false, 'invoices_client_id_index');
        $this->forge->addKey('due_date', false, false, 'invoices_due_date_index');
        $this->forge->addForeignKey('client_id', 'clients', 'id', '', '', 'invoices_client_id_foreign');
        $this->forge->addForeignKey('quotation_id', 'quotations', 'id', '', 'SET NULL', 'invoices_quotation_id_foreign');
        $this->forge->addForeignKey('user_id', 'users', 'id', '', 'CASCADE', 'invoices_user_id_foreign');
        $this->forge->createTable('invoices', true, ['ENGINE' => 'InnoDB']);

        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'invoice_id' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => false],
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
        $this->forge->addKey('invoice_id', false, false, 'invoice_items_invoice_id_index');
        $this->forge->addForeignKey('invoice_id', 'invoices', 'id', '', 'CASCADE', 'invoice_items_invoice_id_foreign');
        $this->forge->addForeignKey('product_id', 'products', 'id', '', 'SET NULL', 'invoice_items_product_id_foreign');
        $this->forge->createTable('invoice_items', true, ['ENGINE' => 'InnoDB']);

        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'invoice_id' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => false],
            'transaction_id' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
            'payment_date' => ['type' => 'DATE', 'null' => false],
            'amount' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'null' => false],
            'payment_method' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'reference_number' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'attachment_path' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'notes' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey(['invoice_id', 'payment_date'], false, false, 'invoice_payments_invoice_id_payment_date_index');
        $this->forge->addForeignKey('invoice_id', 'invoices', 'id', '', 'CASCADE', 'invoice_payments_invoice_id_foreign');
        $this->forge->addForeignKey('transaction_id', 'transactions', 'id', '', 'SET NULL', 'invoice_payments_transaction_id_foreign');
        $this->forge->createTable('invoice_payments', true, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('invoice_payments', true);
        $this->forge->dropTable('invoice_items', true);
        $this->forge->dropTable('invoices', true);
    }
}
