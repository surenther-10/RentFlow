<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<?php
$activeTab = 'grid';
if (service('request')->getVar('page_table')) {
    $activeTab = 'table';
}
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold m-0 text-dark"><i class="fa-solid fa-building text-primary me-2"></i>Properties Catalog</h5>
    <div class="d-flex align-items-center gap-2">
        <ul class="nav nav-pills nav-pills-custom bg-light p-1 rounded-pill no-print" id="propertyTab" role="tablist" style="border: 1px solid var(--border-glass);">
            <li class="nav-item" role="presentation">
                <button class="nav-link <?= $activeTab === 'grid' ? 'active' : '' ?> rounded-pill px-3 py-1.5" id="grid-tab" data-bs-toggle="tab" data-bs-target="#grid-pane" type="button" role="tab" aria-controls="grid-pane" aria-selected="<?= $activeTab === 'grid' ? 'true' : 'false' ?>" style="font-size: 12.5px;"><i class="fa-solid fa-grip me-1.5"></i>Grid Catalog</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link <?= $activeTab === 'table' ? 'active' : '' ?> rounded-pill px-3 py-1.5" id="list-tab" data-bs-toggle="tab" data-bs-target="#list-pane" type="button" role="tab" aria-controls="list-pane" aria-selected="<?= $activeTab === 'table' ? 'true' : 'false' ?>" style="font-size: 12.5px;"><i class="fa-solid fa-list me-1.5"></i>Data Grid</button>
            </li>
        </ul>
        <button class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#addPropertyModal">
            <i class="fa-solid fa-plus me-1.5"></i>Add Property
        </button>
    </div>
</div>

<!-- Search and Filter Bar -->
<div class="row mb-4 no-print">
    <div class="col-12">
        <form action="<?= base_url('properties') ?>" method="GET" class="glass-card py-3 px-4">
            <div class="row g-3 align-items-center">
                <div class="col-lg-4 col-md-6 col-12">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent text-muted border-end-0" style="border-color: rgba(0,0,0,0.08);"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 bg-transparent" placeholder="Search by name, city, state..." value="<?= esc($search ?? '') ?>" style="border-color: rgba(0,0,0,0.08);">
                    </div>
                </div>
                <div class="col-lg-2 col-md-3 col-6">
                    <select name="type" class="form-select bg-transparent" style="border-color: rgba(0,0,0,0.08);">
                        <option value="">All Types</option>
                        <option value="Apartment" <?= ($type ?? '') === 'Apartment' ? 'selected' : '' ?>>Apartment</option>
                        <option value="House" <?= ($type ?? '') === 'House' ? 'selected' : '' ?>>House</option>
                        <option value="Condo" <?= ($type ?? '') === 'Condo' ? 'selected' : '' ?>>Condo</option>
                        <option value="Commercial" <?= ($type ?? '') === 'Commercial' ? 'selected' : '' ?>>Commercial Space</option>
                        <option value="Room" <?= ($type ?? '') === 'Room' ? 'selected' : '' ?>>Single Room</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-3 col-6">
                    <select name="status" class="form-select bg-transparent" style="border-color: rgba(0,0,0,0.08);">
                        <option value="">All Status</option>
                        <option value="available" <?= ($status ?? '') === 'available' ? 'selected' : '' ?>>Available</option>
                        <option value="rented" <?= ($status ?? '') === 'rented' ? 'selected' : '' ?>>Rented</option>
                        <option value="maintenance" <?= ($status ?? '') === 'maintenance' ? 'selected' : '' ?>>Maintenance</option>
                    </select>
                </div>
                <div class="col-lg-4 col-md-12 col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm px-3.5 flex-grow-1"><i class="fa-solid fa-filter me-1.5"></i>Filter</button>
                    <?php if (!empty($search) || !empty($type) || !empty($status)) : ?>
                        <a href="<?= base_url('properties') ?>" class="btn btn-outline-secondary btn-sm px-3.5"><i class="fa-solid fa-xmark"></i></a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="tab-content" id="propertyTabContent">
    <!-- GRID VIEW TAB -->
    <div class="tab-pane fade <?= $activeTab === 'grid' ? 'show active' : '' ?>" id="grid-pane" role="tabpanel" aria-labelledby="grid-tab" tabindex="0">
        <div class="row g-4 mb-5">
            <?php if (empty($propertiesGrid)) : ?>
                <div class="col-12 text-center text-muted py-5">No properties registered.</div>
            <?php else : ?>
                <?php foreach ($propertiesGrid as $property) : ?>
                    <div class="col-xl-3 col-lg-4 col-md-6 col-12">
                        <div class="glass-card p-0 overflow-hidden h-100 d-flex flex-column">
                            <div style="position: relative; height: 165px;">
                                <?php if (!empty($property['image']) && file_exists(FCPATH . $property['image'])) : ?>
                                    <img src="<?= base_url($property['image']) ?>" alt="thumbnail" class="w-100 h-100" style="object-fit: cover;">
                                <?php else : ?>
                                    <div class="w-100 h-100 bg-secondary bg-opacity-10 d-flex align-items-center justify-content-center text-muted">
                                        <i class="fa-solid fa-image fs-1 opacity-20"></i>
                                    </div>
                                <?php endif; ?>
                                <span class="badge-status <?= strtolower($property['availability_status']) ?>" style="position: absolute; top: 12px; right: 12px;">
                                    <?= ucfirst($property['availability_status']) ?>
                                </span>
                            </div>
                            <div class="p-3.5 flex-grow-1 d-flex flex-column justify-content-between">
                                <div>
                                    <h6 class="fw-bold text-dark mb-1"><?= esc($property['name']) ?></h6>
                                    <p class="text-muted mb-2.5" style="font-size: 12.5px;"><i class="fa-solid fa-location-dot me-1.5 text-danger"></i><?= esc($property['city']) ? esc($property['city']) . ', ' . esc($property['state']) : 'No Location' ?></p>
                                    <div class="d-flex align-items-center gap-3 text-muted mb-3.5" style="font-size: 12.5px;">
                                        <span><i class="fa-solid fa-door-open me-1 text-primary"></i><?= esc($property['rooms']) ?> BHK</span>
                                        <span><i class="fa-solid fa-home me-1 text-info"></i><?= esc($property['type']) ?></span>
                                    </div>
                                </div>
                                <div class="pt-2.5 border-top d-flex align-items-center justify-content-between">
                                    <span class="fw-bold text-success fs-5">₹<?= number_format($property['rent_amount'], 2) ?><small class="text-muted" style="font-size: 11px;">/mo</small></span>
                                    <div class="btn-actions">
                                        <button class="btn btn-sm btn-outline-secondary p-1 px-2.5 btn-view" data-id="<?= $property['id'] ?>" title="View details"><i class="fa-solid fa-eye text-info"></i></button>
                                        <button class="btn btn-sm btn-outline-secondary p-1 px-2.5 btn-edit" data-id="<?= $property['id'] ?>" title="Edit"><i class="fa-solid fa-pen-to-square text-muted"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Independent Grid Pagination -->
        <?php if (isset($pager) && $pager->getPageCount('grid') > 1) : 
            $details = $pager->getDetails('grid');
            $currentPage = $details['currentPage'];
            $pageCount = $details['pageCount'];
            
            $queryParams = [];
            if (!empty($search)) $queryParams['search'] = $search;
            if (!empty($type)) $queryParams['type'] = $type;
            if (!empty($status)) $queryParams['status'] = $status;
            if (service('request')->getVar('page_table')) {
                $queryParams['page_table'] = service('request')->getVar('page_table');
            }
            
            $getPageUrl = function($page) use ($queryParams) {
                $params = array_merge($queryParams, ['page_grid' => $page]);
                return base_url('properties') . '?' . http_build_query($params);
            };
        ?>
            <div class="d-flex justify-content-center mt-2 mb-4 no-print">
                <nav aria-label="Properties Grid pagination">
                    <ul class="pagination pagination-sm m-0 align-items-center gap-1">
                        <li class="page-item <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link rounded-circle d-flex align-items-center justify-content-center" 
                               href="<?= ($currentPage > 1) ? $getPageUrl($currentPage - 1) : '#' ?>" 
                               style="width: 32px; height: 32px; border-color: rgba(0,0,0,0.05); background: rgba(255,255,255,0.7); color: var(--primary-color);">
                                <i class="fa-solid fa-chevron-left" style="font-size: 11px;"></i>
                            </a>
                        </li>
                        <?php for ($p = 1; $p <= $pageCount; $p++) : ?>
                            <li class="page-item <?= ($p == $currentPage) ? 'active' : '' ?>">
                                <a class="page-link rounded-circle d-flex align-items-center justify-content-center fw-bold" 
                                   href="<?= $getPageUrl($p) ?>" 
                                   style="width: 32px; height: 32px; border-color: rgba(0,0,0,0.05); <?= ($p == $currentPage) ? 'background: var(--primary-gradient); color: #fff; border: none;' : 'background: rgba(255,255,255,0.7); color: var(--text-primary);' ?> font-size: 12px;">
                                    <?= $p ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= ($currentPage >= $pageCount) ? 'disabled' : '' ?>">
                            <a class="page-link rounded-circle d-flex align-items-center justify-content-center" 
                               href="<?= ($currentPage < $pageCount) ? $getPageUrl($currentPage + 1) : '#' ?>" 
                               style="width: 32px; height: 32px; border-color: rgba(0,0,0,0.05); background: rgba(255,255,255,0.7); color: var(--primary-color);">
                                <i class="fa-solid fa-chevron-right" style="font-size: 11px;"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        <?php endif; ?>
    </div>

    <!-- LIST VIEW TAB -->
    <div class="tab-pane fade <?= $activeTab === 'table' ? 'show active' : '' ?>" id="list-pane" role="tabpanel" aria-labelledby="list-tab" tabindex="0">
        <!-- Table Card -->
        <div class="custom-table-card">
            <div class="table-responsive">
                <table class="table custom-table table-hover properties-table">
                    <thead>
                        <tr>
                            <th>Property Info</th>
                            <th>Location</th>
                            <th>Rooms</th>
                            <th>Monthly Rent</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($propertiesTable as $property) : ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <?php if (!empty($property['image']) && file_exists(FCPATH . $property['image'])) : ?>
                                            <img src="<?= base_url($property['image']) ?>" alt="thumbnail" class="prop-thumbnail">
                                        <?php else : ?>
                                            <div class="prop-thumbnail d-flex align-items-center justify-content-center text-muted">
                                                <i class="fa-solid fa-image opacity-40"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <span class="fw-bold text-dark d-block"><?= esc($property['name']) ?></span>
                                            <small class="text-muted" style="font-size: 11px;"><?= esc($property['type']) ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td><?= esc($property['city']) ? esc($property['city']) . ', ' . esc($property['state']) : 'Not Provided' ?></td>
                                <td><?= esc($property['rooms']) ?> BHK</td>
                                <td class="fw-bold text-success">₹<?= number_format($property['rent_amount'], 2) ?></td>
                                <td>
                                    <span class="badge-status <?= strtolower($property['availability_status']) ?>">
                                        <?= ucfirst($property['availability_status']) ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-actions justify-content-end">
                                        <button class="btn btn-sm btn-outline-secondary btn-view" data-id="<?= $property['id'] ?>" title="View Details"><i class="fa-solid fa-eye text-info"></i></button>
                                        <button class="btn btn-sm btn-outline-secondary btn-edit" data-id="<?= $property['id'] ?>" title="Edit"><i class="fa-solid fa-pen-to-square text-muted"></i></button>
                                        <a href="<?= base_url('properties/delete/' . $property['id']) ?>" class="btn btn-sm btn-outline-secondary" onclick="return confirm('Are you sure you want to delete this property? This will remove all associated images and leases.');" title="Delete"><i class="fa-solid fa-trash text-danger"></i></a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Independent Table Pagination -->
        <?php if (isset($pager) && $pager->getPageCount('table') > 1) : 
            $details = $pager->getDetails('table');
            $currentPage = $details['currentPage'];
            $pageCount = $details['pageCount'];
            
            $queryParams = [];
            if (!empty($search)) $queryParams['search'] = $search;
            if (!empty($type)) $queryParams['type'] = $type;
            if (!empty($status)) $queryParams['status'] = $status;
            if (service('request')->getVar('page_grid')) {
                $queryParams['page_grid'] = service('request')->getVar('page_grid');
            }
            
            $getPageUrl = function($page) use ($queryParams) {
                $params = array_merge($queryParams, ['page_table' => $page]);
                return base_url('properties') . '?' . http_build_query($params);
            };
        ?>
            <div class="d-flex justify-content-center mt-2 mb-4 no-print">
                <nav aria-label="Properties Table pagination">
                    <ul class="pagination pagination-sm m-0 align-items-center gap-1">
                        <li class="page-item <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link rounded-circle d-flex align-items-center justify-content-center" 
                               href="<?= ($currentPage > 1) ? $getPageUrl($currentPage - 1) : '#' ?>" 
                               style="width: 32px; height: 32px; border-color: rgba(0,0,0,0.05); background: rgba(255,255,255,0.7); color: var(--primary-color);">
                                <i class="fa-solid fa-chevron-left" style="font-size: 11px;"></i>
                            </a>
                        </li>
                        <?php for ($p = 1; $p <= $pageCount; $p++) : ?>
                            <li class="page-item <?= ($p == $currentPage) ? 'active' : '' ?>">
                                <a class="page-link rounded-circle d-flex align-items-center justify-content-center fw-bold" 
                                   href="<?= $getPageUrl($p) ?>" 
                                   style="width: 32px; height: 32px; border-color: rgba(0,0,0,0.05); <?= ($p == $currentPage) ? 'background: var(--primary-gradient); color: #fff; border: none;' : 'background: rgba(255,255,255,0.7); color: var(--text-primary);' ?> font-size: 12px;">
                                    <?= $p ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= ($currentPage >= $pageCount) ? 'disabled' : '' ?>">
                            <a class="page-link rounded-circle d-flex align-items-center justify-content-center" 
                               href="<?= ($currentPage < $pageCount) ? $getPageUrl($currentPage + 1) : '#' ?>" 
                               style="width: 32px; height: 32px; border-color: rgba(0,0,0,0.05); background: rgba(255,255,255,0.7); color: var(--primary-color);">
                                <i class="fa-solid fa-chevron-right" style="font-size: 11px;"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Pagination -->
<?php if (isset($pager) && $pager->getPageCount() > 1) : 
    $details = $pager->getDetails();
    $currentPage = $details['currentPage'];
    $pageCount = $details['pageCount'];
    
    // Build query string array to preserve GET parameters
    $queryParams = [];
    if (!empty($search)) $queryParams['search'] = $search;
    if (!empty($type)) $queryParams['type'] = $type;
    if (!empty($status)) $queryParams['status'] = $status;
    
    // Helper function to build page URL preserving query parameters
    $getPageUrl = function($page) use ($queryParams) {
        $params = array_merge($queryParams, ['page' => $page]);
        return base_url('properties') . '?' . http_build_query($params);
    };
?>
    <div class="d-flex justify-content-center mt-2 mb-4 no-print">
        <nav aria-label="Properties pagination">
            <ul class="pagination pagination-sm m-0 align-items-center gap-1">
                <!-- Previous Button -->
                <li class="page-item <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
                    <a class="page-link rounded-circle d-flex align-items-center justify-content-center" 
                       href="<?= ($currentPage > 1) ? $getPageUrl($currentPage - 1) : '#' ?>" 
                       style="width: 32px; height: 32px; border-color: rgba(0,0,0,0.05); background: rgba(255,255,255,0.7); color: var(--primary-color);">
                        <i class="fa-solid fa-chevron-left" style="font-size: 11px;"></i>
                    </a>
                </li>
                
                <!-- Page Numbers -->
                <?php for ($p = 1; $p <= $pageCount; $p++) : ?>
                    <li class="page-item <?= ($p == $currentPage) ? 'active' : '' ?>">
                        <a class="page-link rounded-circle d-flex align-items-center justify-content-center fw-bold" 
                           href="<?= $getPageUrl($p) ?>" 
                           style="width: 32px; height: 32px; border-color: rgba(0,0,0,0.05); <?= ($p == $currentPage) ? 'background: var(--primary-gradient); color: #fff; border: none;' : 'background: rgba(255,255,255,0.7); color: var(--text-primary);' ?> font-size: 12px;">
                            <?= $p ?>
                        </a>
                    </li>
                <?php endfor; ?>
                
                <!-- Next Button -->
                <li class="page-item <?= ($currentPage >= $pageCount) ? 'disabled' : '' ?>">
                    <a class="page-link rounded-circle d-flex align-items-center justify-content-center" 
                       href="<?= ($currentPage < $pageCount) ? $getPageUrl($currentPage + 1) : '#' ?>" 
                       style="width: 32px; height: 32px; border-color: rgba(0,0,0,0.05); background: rgba(255,255,255,0.7); color: var(--primary-color);">
                        <i class="fa-solid fa-chevron-right" style="font-size: 11px;"></i>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('modals') ?>

<!-- ADD PROPERTY MODAL -->
<div class="modal fade" id="addPropertyModal" tabindex="-1" aria-labelledby="addPropertyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPropertyModalLabel"><i class="fa-solid fa-plus text-primary me-2"></i>Add Property</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('properties/store') ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="name" class="form-label">Property Name</label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Skyline Luxury Room 102" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="type" class="form-label">Type</label>
                            <select name="type" class="form-select" required>
                                <option value="Apartment">Apartment</option>
                                <option value="House">House</option>
                                <option value="Condo">Condo</option>
                                <option value="Commercial">Commercial Space</option>
                                <option value="Room">Single Room</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea name="address" class="form-control" rows="2" required></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="city" class="form-label">City</label>
                            <input type="text" name="city" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="state" class="form-label">State</label>
                            <input type="text" name="state" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="pincode" class="form-label">Pincode</label>
                            <input type="text" name="pincode" class="form-control" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="rent_amount" class="form-label">Monthly Rent Amount (INR)</label>
                            <input type="number" name="rent_amount" class="form-control" step="0.01" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="rooms" class="form-label">Number of Rooms (BHK)</label>
                            <input type="number" name="rooms" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="availability_status" class="form-label">Status</label>
                            <select name="availability_status" class="form-select" required>
                                <option value="available">Available</option>
                                <option value="rented">Rented</option>
                                <option value="maintenance">Maintenance</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="Enter specifications..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="images" class="form-label">Upload Multiple Images</label>
                        <input type="file" name="images[]" class="form-control" accept="image/*" multiple>
                        <small class="text-muted">You can select multiple files at once.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Property</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- EDIT PROPERTY MODAL -->
<div class="modal fade" id="editPropertyModal" tabindex="-1" aria-labelledby="editPropertyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editPropertyModalLabel"><i class="fa-solid fa-pen-to-square text-primary me-2"></i>Edit Property Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="editPropertyForm" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="edit_name" class="form-label">Property Name</label>
                            <input type="text" name="name" id="edit_name" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_type" class="form-label">Type</label>
                            <select name="type" id="edit_type" class="form-select" required>
                                <option value="Apartment">Apartment</option>
                                <option value="House">House</option>
                                <option value="Condo">Condo</option>
                                <option value="Commercial">Commercial Space</option>
                                <option value="Room">Single Room</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="edit_address" class="form-label">Address</label>
                        <textarea name="address" id="edit_address" class="form-control" rows="2" required></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="edit_city" class="form-label">City</label>
                            <input type="text" name="city" id="edit_city" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_state" class="form-label">State</label>
                            <input type="text" name="state" id="edit_state" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_pincode" class="form-label">Pincode</label>
                            <input type="text" name="pincode" id="edit_pincode" class="form-control" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="edit_rent_amount" class="form-label">Monthly Rent Amount (INR)</label>
                            <input type="number" name="rent_amount" id="edit_rent_amount" class="form-control" step="0.01" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_rooms" class="form-label">Number of Rooms (BHK)</label>
                            <input type="number" name="rooms" id="edit_rooms" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_availability_status" class="form-label">Status</label>
                            <select name="availability_status" id="edit_availability_status" class="form-select" required>
                                <option value="available">Available</option>
                                <option value="rented">Rented</option>
                                <option value="maintenance">Maintenance</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Description</label>
                        <textarea name="description" id="edit_description" class="form-control" rows="2"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="edit_images" class="form-label">Upload Additional Images</label>
                        <input type="file" name="images[]" class="form-control" accept="image/*" multiple>
                    </div>

                    <!-- Existing Images Grid -->
                    <div id="edit_existing_images_section" class="mb-3">
                        <label class="form-label d-block">Manage Property Images</label>
                        <div class="row g-2" id="edit_images_grid">
                            <!-- Populated dynamically via JS -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Property</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- VIEW PROPERTY DETAILS MODAL -->
<div class="modal fade" id="viewPropertyModal" tabindex="-1" aria-labelledby="viewPropertyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewPropertyModalLabel"><i class="fa-solid fa-house text-primary me-2"></i>Property Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- Carousel column -->
                    <div class="col-md-6 col-12 mb-3">
                        <div id="propertyImageCarousel" class="carousel slide bg-light border rounded overflow-hidden" data-bs-ride="carousel" style="height: 250px;">
                            <div class="carousel-inner h-100" id="carousel_items">
                                <!-- Populated dynamically -->
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#propertyImageCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#propertyImageCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Information column -->
                    <div class="col-md-6 col-12">
                        <h4 class="fw-bold text-dark mb-1" id="view_name"></h4>
                        <span class="badge bg-primary text-uppercase mb-3" id="view_type"></span>
                        
                        <div class="mb-3 text-muted" style="font-size: 13.5px;">
                            <i class="fa-solid fa-location-dot text-danger me-2"></i><span id="view_address"></span>
                        </div>
                        <div class="row text-center bg-light border rounded p-3 mb-3 mx-0">
                            <div class="col-6 border-end">
                                <span class="text-muted d-block" style="font-size: 11.5px;">Monthly Rent</span>
                                <strong class="text-success fs-5">₹<span id="view_rent"></span></strong>
                            </div>
                            <div class="col-6">
                                <span class="text-muted d-block" style="font-size: 11.5px;">Configuration</span>
                                <strong class="text-dark fs-5"><span id="view_rooms"></span> BHK</strong>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-3">
                    <h6 class="fw-bold text-dark mb-2">Description</h6>
                    <p class="text-muted" id="view_description" style="font-size: 13.5px;"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Initialize DataTable with paging, search, and info disabled to stay in sync with grid view
        if ($.fn.DataTable.isDataTable('.properties-table')) {
            $('.properties-table').DataTable().destroy();
        }
        $('.properties-table').DataTable({
            "paging": false,
            "searching": false,
            "info": false,
            "ordering": true,
            "responsive": true
        });

        var baseUrl = '<?= base_url() ?>/';
        <?php
        $ajaxBaseUrl = site_url();
        if (strpos($ajaxBaseUrl, 'index.php') !== false) {
            $ajaxBaseUrl .= '/';
        } else {
            $ajaxBaseUrl = rtrim($ajaxBaseUrl, '/') . '/';
        }
        ?>
        var ajaxUrl = '<?= $ajaxBaseUrl ?>';

        // Details Modal
        $('.btn-view').click(function() {
            var id = $(this).data('id');
            $.ajax({
                url: ajaxUrl + 'properties/details/' + id,
                type: 'GET',
                success: function(res) {
                    if (res.status === 'success') {
                        var p = res.data;
                        $('#view_name').text(p.name);
                        $('#view_type').text(p.type);
                        $('#view_address').text(p.address + ', ' + p.city + ', ' + p.state + ' - ' + p.pincode);
                        $('#view_rent').text(parseFloat(p.rent_amount).toLocaleString('en-IN', {minimumFractionDigits: 2}));
                        $('#view_rooms').text(p.rooms);
                        $('#view_description').text(p.description || 'No description provided.');

                        // Carousel Images
                        var carouselHtml = '';
                        if (p.images.length === 0) {
                            carouselHtml = '<div class="carousel-item active h-100 d-flex align-items-center justify-content-center text-muted"><i class="fa-solid fa-image fs-1 opacity-25"></i></div>';
                        } else {
                            p.images.forEach(function(img, idx) {
                                var activeClass = idx === 0 ? 'active' : '';
                                carouselHtml += '<div class="carousel-item ' + activeClass + ' h-100"><img src="' + baseUrl + img.image_path + '" class="d-block w-100 h-100" style="object-fit: cover;"></div>';
                            });
                        }
                        $('#carousel_items').html(carouselHtml);

                        $('#viewPropertyModal').modal('show');
                    }
                }
            });
        });

        // Edit Modal
        $('.btn-edit').click(function() {
            var id = $(this).data('id');
            $('#editPropertyForm').attr('action', ajaxUrl + 'properties/update/' + id);
            $.ajax({
                url: ajaxUrl + 'properties/details/' + id,
                type: 'GET',
                success: function(res) {
                    if (res.status === 'success') {
                        var p = res.data;
                        $('#edit_name').val(p.name);
                        $('#edit_type').val(p.type);
                        $('#edit_address').val(p.address);
                        $('#edit_city').val(p.city);
                        $('#edit_state').val(p.state);
                        $('#edit_pincode').val(p.pincode);
                        $('#edit_rent_amount').val(p.rent_amount);
                        $('#edit_rooms').val(p.rooms);
                        $('#edit_availability_status').val(p.availability_status);
                        $('#edit_description').val(p.description);

                        // Images deletion grid
                        var imagesGridHtml = '';
                        if (p.images.length === 0) {
                            imagesGridHtml = '<div class="col-12 text-muted" style="font-size: 13px;">No uploaded images.</div>';
                        } else {
                            p.images.forEach(function(img) {
                                imagesGridHtml += '<div class="col-3 position-relative img-wrapper" id="img-item-' + img.id + '" style="height: 80px;">' +
                                    '<img src="' + baseUrl + img.image_path + '" class="w-100 h-100 rounded" style="object-fit: cover;">' +
                                    '<button type="button" class="btn btn-danger btn-sm p-0 rounded-circle btn-delete-img" data-imgid="' + img.id + '" style="position: absolute; top: -5px; right: -5px; width: 22px; height: 22px;"><i class="fa-solid fa-xmark" style="font-size: 10px;"></i></button>' +
                                    '</div>';
                            });
                        }
                        $('#edit_images_grid').html(imagesGridHtml);
                        $('#editPropertyModal').modal('show');
                    }
                }
            });
        });

        // Handle AJAX Image Deletion
        $(document).on('click', '.btn-delete-img', function() {
            var imgId = $(this).data('imgid');
            if (confirm('Delete this image permanently?')) {
                $.ajax({
                    url: ajaxUrl + 'properties/deleteImage/' + imgId,
                    type: 'GET',
                    success: function(res) {
                        if (res.status === 'success') {
                            $('#img-item-' + imgId).fadeOut(300, function() { $(this).remove(); });
                        }
                    }
                });
            }
        });
    });
</script>
<?= $this->endSection() ?>
