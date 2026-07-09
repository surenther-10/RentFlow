<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// Auth routes
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::attemptLogin');
$routes->get('register', 'Auth::register');
$routes->post('register', 'Auth::attemptRegister');
$routes->get('logout', 'Auth::logout');
$routes->get('forgot-password', 'Auth::forgotPassword');
$routes->post('forgot-password', 'Auth::attemptForgotPassword');

// Protected routes (filter: auth)
$routes->group('', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'Dashboard::index');
    $routes->get('dashboard', 'Dashboard::index');
    $routes->get('dashboard/markNotificationsRead', 'Dashboard::markNotificationsRead');
    $routes->get('dashboard/search', 'Dashboard::search');
    
    $routes->get('change-password', 'Auth::changePassword');
    $routes->post('change-password', 'Auth::attemptChangePassword');

    $routes->get('profile', 'Auth::profile');
    $routes->post('profile', 'Auth::attemptUpdateProfile');

    // Rent list & receipt (everyone can view their own, but only admin/owner can add/delete)
    $routes->get('rent', 'Rent::index');
    $routes->get('rent/receipt/(:num)', 'Rent::receipt/$1');
    $routes->get('rent/download/(:num)', 'Rent::downloadPdf/$1');

    // Maintenance (everyone can view/create, but only admin/owner can assign/delete)
    $routes->get('maintenance', 'Maintenance::index');
    $routes->get('maintenance/details/(:num)', 'Maintenance::details/$1');
    $routes->post('maintenance/store', 'Maintenance::store');
    $routes->post('maintenance/comment/(:num)', 'Maintenance::addComment/$1');
});

// Admin & Owner routes (filter: auth:admin,owner)
$routes->group('', ['filter' => 'auth:admin,owner'], function($routes) {
    // Properties
    $routes->get('properties', 'Properties::index');
    $routes->get('properties/details/(:num)', 'Properties::details/$1');
    $routes->post('properties/store', 'Properties::store');
    $routes->post('properties/update/(:num)', 'Properties::update/$1');
    $routes->get('properties/delete/(:num)', 'Properties::delete/$1');
    $routes->get('properties/deleteImage/(:num)', 'Properties::deleteImage/$1');

    // Tenants
    $routes->get('tenants', 'Tenants::index');
    $routes->get('tenants/details/(:num)', 'Tenants::details/$1');
    $routes->post('tenants/store', 'Tenants::store');
    $routes->post('tenants/update/(:num)', 'Tenants::update/$1');
    $routes->get('tenants/delete/(:num)', 'Tenants::delete/$1');

    // Leases
    $routes->get('leases', 'Leases::index');
    $routes->get('leases/details/(:num)', 'Leases::details/$1');
    $routes->post('leases/store', 'Leases::store');
    $routes->post('leases/update/(:num)', 'Leases::update/$1');
    $routes->post('leases/attemptRenew/(:num)', 'Leases::attemptRenew/$1');
    $routes->get('leases/delete/(:num)', 'Leases::delete/$1');

    // Rent CRUD
    $routes->post('rent/store', 'Rent::store');
    $routes->get('rent/delete/(:num)', 'Rent::delete/$1');

    // Maintenance ticket update/assign
    $routes->post('maintenance/assign/(:num)', 'Maintenance::assign/$1');
    $routes->post('maintenance/updateStatus/(:num)', 'Maintenance::updateStatus/$1');
    $routes->get('maintenance/delete/(:num)', 'Maintenance::delete/$1');

    // Reports
    $routes->get('reports', 'Reports::index');
    $routes->get('reports/exportRent', 'Reports::exportRent');
    $routes->get('reports/exportOccupancy', 'Reports::exportOccupancy');
    $routes->get('reports/exportTenants', 'Reports::exportTenants');
    $routes->get('reports/exportMaintenance', 'Reports::exportMaintenance');
    $routes->get('reports/exportRevenue', 'Reports::exportRevenue');
});

$routes->group('admin', ['filter' => 'auth:admin'], function($routes) {
    $routes->get('dashboard', 'Dashboard::admin');
    $routes->get('users', 'Users::index');
    $routes->get('users/details/(:num)', 'Users::details/$1');
    $routes->post('users/store', 'Users::store');
    $routes->post('users/update/(:num)', 'Users::update/$1');
    $routes->get('users/delete/(:num)', 'Users::delete/$1');

    $routes->get('settings', 'Settings::index');
    $routes->post('settings/update', 'Settings::update');
});

// Owner only dashboard
$routes->get('owner/dashboard', 'Dashboard::owner', ['filter' => 'auth:owner']);

// Tenant only dashboard
$routes->get('tenant/dashboard', 'Dashboard::tenant', ['filter' => 'auth:tenant']);
