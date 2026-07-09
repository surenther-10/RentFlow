<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRentFlowModules extends Migration
{
    public function up()
    {
        // 1. ROLES TABLE
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'description' => [
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
        $this->forge->addUniqueKey('name');
        $this->forge->createTable('roles');

        // 2. PROPERTY IMAGES TABLE
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'property_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'image_path' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
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
        $this->forge->createTable('property_images');

        // 3. MAINTENANCE COMMENTS TABLE
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'ticket_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'comment' => [
                'type' => 'TEXT',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('maintenance_comments');

        // 4. NOTIFICATIONS TABLE
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
            ],
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'message' => [
                'type' => 'TEXT',
            ],
            'is_read' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'type' => [
                'type'       => 'VARCHAR',
                'constraint' => '50', // rent, lease, maintenance, system
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
        $this->forge->createTable('notifications');

        // 5. ALTER PROPERTIES TABLE
        $this->forge->addColumn('properties', [
            'city' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
                'after'      => 'address',
            ],
            'state' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
                'after'      => 'city',
            ],
            'pincode' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'null'       => true,
                'after'      => 'state',
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
                'after'      => 'rooms',
            ],
        ]);

        // 6. ALTER TENANTS TABLE
        $this->forge->addColumn('tenants', [
            'profile_photo' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
                'after'      => 'address',
            ]
        ]);

        // 7. ALTER MAINTENANCE TICKETS TABLE
        $this->forge->addColumn('maintenance_tickets', [
            'attachment_path' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
                'after'      => 'assigned_technician',
            ]
        ]);

        // 8. ALTER RENT PAYMENTS TABLE
        $this->forge->addColumn('rent_payments', [
            'doc_path' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
                'after'      => 'receipt_number',
            ]
        ]);

        // 9. ALTER USERS TABLE (Add role_id)
        $this->forge->addColumn('users', [
            'role_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'password',
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'role_id');
        $this->forge->dropColumn('rent_payments', 'doc_path');
        $this->forge->dropColumn('maintenance_tickets', 'attachment_path');
        $this->forge->dropColumn('tenants', 'profile_photo');
        
        $this->forge->dropColumn('properties', 'city');
        $this->forge->dropColumn('properties', 'state');
        $this->forge->dropColumn('properties', 'pincode');
        $this->forge->dropColumn('properties', 'description');

        $this->forge->dropTable('notifications', true);
        $this->forge->dropTable('maintenance_comments', true);
        $this->forge->dropTable('property_images', true);
        $this->forge->dropTable('roles', true);
    }
}
