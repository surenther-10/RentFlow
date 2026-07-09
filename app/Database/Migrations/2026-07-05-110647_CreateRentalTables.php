<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRentalTables extends Migration
{
    public function up()
    {
        // 1. USERS TABLE
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'username' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'password' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'role' => [
                'type'       => 'VARCHAR',
                'constraint' => '50', // admin, owner, tenant
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => '20', // active, inactive
                'default'    => 'active',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('username');
        $this->forge->addUniqueKey('email');
        $this->forge->createTable('users');

        // 2. PROPERTIES TABLE
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'type' => [
                'type'       => 'VARCHAR',
                'constraint' => '100', // Apartment, House, Condo, Commercial, etc.
            ],
            'address' => [
                'type' => 'TEXT',
            ],
            'rent_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'rooms' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'availability_status' => [
                'type'       => 'VARCHAR',
                'constraint' => '50', // available, rented, maintenance
                'default'    => 'available',
            ],
            'image' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('properties');

        // 3. TENANTS TABLE
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'mobile' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'aadhaar_number' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'null'       => true,
            ],
            'pan_number' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'null'       => true,
            ],
            'address' => [
                'type' => 'TEXT',
            ],
            'doc_path' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('tenants');

        // 4. LEASES TABLE
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'agreement_number' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'property_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'tenant_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'start_date' => [
                'type' => 'DATE',
            ],
            'end_date' => [
                'type' => 'DATE',
            ],
            'security_deposit' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'monthly_rent' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'doc_path' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => '50', // active, expired, terminated
                'default'    => 'active',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('agreement_number');
        $this->forge->createTable('leases');

        // 5. RENT PAYMENTS TABLE
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'lease_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'payment_date' => [
                'type' => 'DATE',
            ],
            'payment_method' => [
                'type'       => 'VARCHAR',
                'constraint' => '50', // Cash, UPI, Bank Transfer, Online Payment Gateway
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => '50', // Paid, Pending, Overdue
                'default'    => 'Paid',
            ],
            'receipt_number' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('receipt_number');
        $this->forge->createTable('rent_payments');

        // 6. MAINTENANCE TICKETS TABLE
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'tenant_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'property_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'description' => [
                'type' => 'TEXT',
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => '50', // Open, In Progress, Completed, Closed
                'default'    => 'Open',
            ],
            'assigned_technician' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('maintenance_tickets');
    }

    public function down()
    {
        $this->forge->dropTable('maintenance_tickets', true);
        $this->forge->dropTable('rent_payments', true);
        $this->forge->dropTable('leases', true);
        $this->forge->dropTable('tenants', true);
        $this->forge->dropTable('properties', true);
        $this->forge->dropTable('users', true);
    }
}
