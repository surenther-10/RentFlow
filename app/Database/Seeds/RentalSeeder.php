<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RentalSeeder extends Seeder
{
    public function run()
    {
        // 0. Seed Roles
        $roles = [
            [
                'id'          => 1,
                'name'        => 'admin',
                'description' => 'System Administrator with full access',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'id'          => 2,
                'name'        => 'owner',
                'description' => 'Property Owner with manager privileges',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'id'          => 3,
                'name'        => 'tenant',
                'description' => 'Tenant with renter access',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ]
        ];

        foreach ($roles as $role) {
            // Delete if exists first to avoid duplicates
            $this->db->table('roles')->where('id', $role['id'])->delete();
            $this->db->table('roles')->insert($role);
        }

        // Passwords hashed using bcrypt
        $passwordHash = password_hash('password123', PASSWORD_BCRYPT);

        // 1. Seed Users
        $users = [
            [
                'id'         => 1,
                'username'   => 'admin',
                'email'      => 'admin@rental.com',
                'password'   => $passwordHash,
                'role'       => 'admin',
                'role_id'    => 1,
                'status'     => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id'         => 2,
                'username'   => 'owner',
                'email'      => 'owner@rental.com',
                'password'   => $passwordHash,
                'role'       => 'owner',
                'role_id'    => 2,
                'status'     => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id'         => 3,
                'username'   => 'tenant',
                'email'      => 'tenant@rental.com',
                'password'   => $passwordHash,
                'role'       => 'tenant',
                'role_id'    => 3,
                'status'     => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id'         => 4,
                'username'   => 'tenant2',
                'email'      => 'tenant2@rental.com',
                'password'   => $passwordHash,
                'role'       => 'tenant',
                'role_id'    => 3,
                'status'     => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        ];

        foreach ($users as $user) {
            $this->db->table('users')->where('id', $user['id'])->delete();
            $this->db->table('users')->insert($user);
        }

        // 2. Seed Properties
        $properties = [
            [
                'id'                  => 1,
                'name'                => 'Sunset Heights Apt 4B',
                'type'                => 'Apartment',
                'address'             => '452 Park Avenue, Sector 5',
                'city'                => 'New Delhi',
                'state'               => 'Delhi',
                'pincode'             => '110001',
                'rent_amount'         => 18500.00,
                'rooms'               => 2,
                'description'         => 'Premium 2 BHK apartment with beautiful sunset views, modern kitchen fittings, and 24/7 water backup.',
                'availability_status' => 'rented',
                'image'               => null,
                'created_at'          => date('Y-m-d H:i:s'),
                'updated_at'          => date('Y-m-d H:i:s'),
            ],
            [
                'id'                  => 2,
                'name'                => 'Green Villa House',
                'type'                => 'House',
                'address'             => '12 Rose Gardens, ECR Road',
                'city'                => 'Chennai',
                'state'               => 'Tamil Nadu',
                'pincode'             => '600041',
                'rent_amount'         => 35000.00,
                'rooms'               => 4,
                'description'         => 'Spacious independent 4 BHK villa with a private garden, lawn, car parking space, and solar heating.',
                'availability_status' => 'rented',
                'image'               => null,
                'created_at'          => date('Y-m-d H:i:s'),
                'updated_at'          => date('Y-m-d H:i:s'),
            ],
            [
                'id'                  => 3,
                'name'                => 'Skyline Luxury Condo',
                'type'                => 'Condo',
                'address'             => 'Unit 2202, Block C, Outer Ring Road',
                'city'                => 'Bangalore',
                'state'               => 'Karnataka',
                'pincode'             => '560103',
                'rent_amount'         => 45000.00,
                'rooms'               => 3,
                'description'         => 'High-rise 3 BHK condo featuring panoramic views, gym membership, swimming pool access, and piped gas connection.',
                'availability_status' => 'available',
                'image'               => null,
                'created_at'          => date('Y-m-d H:i:s'),
                'updated_at'          => date('Y-m-d H:i:s'),
            ],
            [
                'id'                  => 4,
                'name'                => 'Metro Plaza Room 102',
                'type'                => 'Commercial',
                'address'             => '102 Main Street, Sector 62',
                'city'                => 'Noida',
                'state'               => 'Uttar Pradesh',
                'pincode'             => '201301',
                'rent_amount'         => 22000.00,
                'rooms'               => 1,
                'description'         => 'Prime commercial office space suited for small businesses, agencies or consulting firms. Ground floor access.',
                'availability_status' => 'maintenance',
                'image'               => null,
                'created_at'          => date('Y-m-d H:i:s'),
                'updated_at'          => date('Y-m-d H:i:s'),
            ],
            [
                'id'                  => 5,
                'name'                => 'Orchard Garden Studio',
                'type'                => 'Apartment',
                'address'             => '202 Orchard Road, Whitefield',
                'city'                => 'Bangalore',
                'state'               => 'Karnataka',
                'pincode'             => '560066',
                'rent_amount'         => 12000.00,
                'rooms'               => 1,
                'description'         => 'Cozy 1 BHK studio apartment with modular kitchen, private balcony, and close proximity to IT parks.',
                'availability_status' => 'available',
                'image'               => null,
                'created_at'          => date('Y-m-d H:i:s'),
                'updated_at'          => date('Y-m-d H:i:s'),
            ],
            [
                'id'                  => 6,
                'name'                => 'Coastal Breeze Rowhouse',
                'type'                => 'House',
                'address'             => '88 Marine Drive',
                'city'                => 'Mumbai',
                'state'               => 'Maharashtra',
                'pincode'             => '400002',
                'rent_amount'         => 28000.00,
                'rooms'               => 3,
                'description'         => 'Fabulous 3 BHK rowhouse facing the sea with private rooftop access, terrace gardens, and covered garage.',
                'availability_status' => 'available',
                'image'               => null,
                'created_at'          => date('Y-m-d H:i:s'),
                'updated_at'          => date('Y-m-d H:i:s'),
            ],
            [
                'id'                  => 7,
                'name'                => 'Oakwood Penthouse 6',
                'type'                => 'Condo',
                'address'             => 'Penthouse 6, Oakwood Towers',
                'city'                => 'Pune',
                'state'               => 'Maharashtra',
                'pincode'             => '411001',
                'rent_amount'         => 55000.00,
                'rooms'               => 4,
                'description'         => 'Luxury 4 BHK penthouse with personal infinity pool, double-height ceilings, automated control features, and 3 parking slots.',
                'availability_status' => 'available',
                'image'               => null,
                'created_at'          => date('Y-m-d H:i:s'),
                'updated_at'          => date('Y-m-d H:i:s'),
            ],
            [
                'id'                  => 8,
                'name'                => 'Prestige Tech Park Office',
                'type'                => 'Commercial',
                'address'             => 'Block A, Prestige Tech Park',
                'city'                => 'Bangalore',
                'state'               => 'Karnataka',
                'pincode'             => '560087',
                'rent_amount'         => 65000.00,
                'rooms'               => 2,
                'description'         => 'Premium commercial tech workspace, fully furnished with conference room, pantry, and centralized AC.',
                'availability_status' => 'available',
                'image'               => null,
                'created_at'          => date('Y-m-d H:i:s'),
                'updated_at'          => date('Y-m-d H:i:s'),
            ],
            [
                'id'                  => 9,
                'name'                => 'Rosewood Cozy Room 12',
                'type'                => 'Room',
                'address'             => '12 Rosewood Guesthouse, Jubilee Hills',
                'city'                => 'Hyderabad',
                'state'               => 'Telangana',
                'pincode'             => '500033',
                'rent_amount'         => 7500.00,
                'rooms'               => 1,
                'description'         => 'Cozy single room with attached bath, ideal for single students or working professionals. Includes Wi-Fi and water bills.',
                'availability_status' => 'available',
                'image'               => null,
                'created_at'          => date('Y-m-d H:i:s'),
                'updated_at'          => date('Y-m-d H:i:s'),
            ],
            [
                'id'                  => 10,
                'name'                => 'Lakeview Heights Room 303',
                'type'                => 'Room',
                'address'             => '303 Lakeview Heights, Mall Road',
                'city'                => 'Udaipur',
                'state'               => 'Rajasthan',
                'pincode'             => '313001',
                'rent_amount'         => 6000.00,
                'rooms'               => 1,
                'description'         => 'Single room with panoramic view of Lake Pichola. Awaiting plumbing servicing.',
                'availability_status' => 'maintenance',
                'image'               => null,
                'created_at'          => date('Y-m-d H:i:s'),
                'updated_at'          => date('Y-m-d H:i:s'),
            ]
        ];

        foreach ($properties as $property) {
            $this->db->table('properties')->where('id', $property['id'])->delete();
            $this->db->table('properties')->insert($property);
        }
        // 2.5 Seed Owners
        $owners = [
            [
                'id'             => 1,
                'user_id'        => 2, // Links to owner
                'name'           => 'Property Owner Manager',
                'mobile'         => '9000100010',
                'email'          => 'owner@rental.com',
                'address'        => 'Admin Suite 101, RentFlow Building, Bangalore',
                'profile_photo'  => null,
                'doc_path'       => null,
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ]
        ];

        foreach ($owners as $owner) {
            $this->db->table('owners')->where('id', $owner['id'])->delete();
            $this->db->table('owners')->insert($owner);
        }

        // 3. Seed Tenants
        $tenants = [
            [
                'id'             => 1,
                'user_id'        => 3, // Links to tenant
                'name'           => 'John Doe',
                'mobile'         => '9876543210',
                'email'          => 'tenant@rental.com',
                'aadhaar_number' => '1234-5678-9012',
                'pan_number'     => 'ABCDE1234F',
                'address'        => 'Flat 3A, Sunny Apartments, Mumbai',
                'profile_photo'  => null,
                'doc_path'       => null,
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'id'             => 2,
                'user_id'        => 4, // Links to tenant2
                'name'           => 'Jane Smith',
                'mobile'         => '8765432109',
                'email'          => 'tenant2@rental.com',
                'aadhaar_number' => '9876-5432-1098',
                'pan_number'     => 'XYZWH5678A',
                'address'        => 'Block 2, Hillview Townships, Pune',
                'profile_photo'  => null,
                'doc_path'       => null,
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ]
        ];

        foreach ($tenants as $tenant) {
            $this->db->table('tenants')->where('id', $tenant['id'])->delete();
            $this->db->table('tenants')->insert($tenant);
        }

        // 4. Seed Leases
        $leases = [
            [
                'id'               => 1,
                'agreement_number' => 'LEASE-2026-001',
                'property_id'      => 1, // Sunset Heights Apt 4B
                'tenant_id'        => 1, // John Doe
                'start_date'       => '2026-01-01',
                'end_date'         => '2026-12-31',
                'security_deposit' => 37000.00,
                'monthly_rent'     => 18500.00,
                'doc_path'         => null,
                'status'           => 'active',
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ],
            [
                'id'               => 2,
                'agreement_number' => 'LEASE-2026-002',
                'property_id'      => 2, // Green Villa House
                'tenant_id'        => 2, // Jane Smith
                'start_date'       => '2025-08-01',
                'end_date'         => '2026-07-31',
                'security_deposit' => 70000.00,
                'monthly_rent'     => 35000.00,
                'doc_path'         => null,
                'status'           => 'active',
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ]
        ];

        foreach ($leases as $lease) {
            $this->db->table('leases')->where('id', $lease['id'])->delete();
            $this->db->table('leases')->insert($lease);
        }

        // 5. Seed Rent Payments
        $payments = [
            [
                'id'             => 1,
                'lease_id'       => 1,
                'amount'         => 18500.00,
                'payment_date'   => '2026-06-05',
                'payment_method' => 'UPI',
                'status'         => 'Paid',
                'receipt_number' => 'REC-2026-001',
                'doc_path'       => null,
                'notes'          => 'June 2026 Rent Payment',
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'id'             => 2,
                'lease_id'       => 1,
                'amount'         => 18500.00,
                'payment_date'   => '2026-05-02',
                'payment_method' => 'Bank Transfer',
                'status'         => 'Paid',
                'receipt_number' => 'REC-2026-002',
                'doc_path'       => null,
                'notes'          => 'May 2026 Rent Payment',
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'id'             => 3,
                'lease_id'       => 2,
                'amount'         => 35000.00,
                'payment_date'   => '2026-06-03',
                'payment_method' => 'Online Payment Gateway',
                'status'         => 'Paid',
                'receipt_number' => 'REC-2026-003',
                'doc_path'       => null,
                'notes'          => 'June 2026 Rent Payment',
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ]
        ];

        foreach ($payments as $payment) {
            $this->db->table('rent_payments')->where('id', $payment['id'])->delete();
            $this->db->table('rent_payments')->insert($payment);
        }

        // 6. Seed Maintenance Tickets
        $tickets = [
            [
                'id'                  => 1,
                'tenant_id'           => 1, // John Doe
                'property_id'         => 1,
                'title'               => 'Leaking tap in kitchen',
                'description'         => 'The kitchen sink tap has been dripping continuously since last night. Please fix.',
                'status'              => 'Open',
                'assigned_technician' => null,
                'attachment_path'     => null,
                'created_at'          => date('Y-m-d H:i:s'),
                'updated_at'          => date('Y-m-d H:i:s'),
            ],
            [
                'id'                  => 2,
                'tenant_id'           => 2, // Jane Smith
                'property_id'         => 2,
                'title'               => 'Electrical short circuit in living room',
                'description' => 'Two plug sockets in the living room sparked and are now dead. Needs immediate attention.',
                'status'              => 'In Progress',
                'assigned_technician' => 'Robert Plumber & Electrician',
                'attachment_path'     => null,
                'created_at'          => date('Y-m-d H:i:s'),
                'updated_at'          => date('Y-m-d H:i:s'),
            ]
        ];

        foreach ($tickets as $ticket) {
            $this->db->table('maintenance_tickets')->where('id', $ticket['id'])->delete();
            $this->db->table('maintenance_tickets')->insert($ticket);
        }

        // 7. Seed Comments
        $comments = [
            [
                'ticket_id'  => 1,
                'user_id'    => 3, // John Doe User
                'comment'    => 'I tried wrapping it with thread tape but water is still spraying out slightly.',
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 hour')),
            ],
            [
                'ticket_id'  => 2,
                'user_id'    => 2, // Owner User
                'comment'    => 'Robert will reach your premises by 4:00 PM today. Please ensure someone is home.',
                'created_at' => date('Y-m-d H:i:s', strtotime('-30 minutes')),
            ]
        ];

        foreach ($comments as $comment) {
            $this->db->table('maintenance_comments')->insert($comment);
        }

        // 8. Seed Notifications
        $notifications = [
            [
                'user_id'    => 1, // Admin
                'title'      => 'New Maintenance Request',
                'message'    => 'John Doe raised a ticket: Leaking tap in kitchen.',
                'is_read'    => 0,
                'type'       => 'maintenance',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'user_id'    => 3, // Tenant (John Doe)
                'title'      => 'Rent Overdue Notice',
                'message'    => 'Rent for June 2026 is due. Please make a payment as soon as possible.',
                'is_read'    => 0,
                'type'       => 'rent',
                'created_at' => date('Y-m-d H:i:s', strtotime('-2 days')),
                'updated_at' => date('Y-m-d H:i:s', strtotime('-2 days')),
            ]
        ];

        foreach ($notifications as $notification) {
            $this->db->table('notifications')->insert($notification);
        }

        // 9. Seed Settings
        $settings = [
            ['key' => 'site_name', 'value' => 'RentFlow SaaS', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['key' => 'currency', 'value' => 'INR', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['key' => 'admin_email', 'value' => 'admin@rental.com', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['key' => 'contact_phone', 'value' => '9876543210', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
        ];

        foreach ($settings as $setting) {
            $this->db->table('settings')->where('key', $setting['key'])->delete();
            $this->db->table('settings')->insert($setting);
        }
    }
}
