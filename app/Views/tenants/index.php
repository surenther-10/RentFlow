<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold m-0 text-dark"><i class="fa-solid fa-user-tie text-primary me-2"></i>Tenants Database</h5>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addTenantModal">
        <i class="fa-solid fa-plus me-2"></i>Add Tenant
    </button>
</div>

<!-- Table Card -->
<div class="custom-table-card">
    <div class="table-responsive">
        <table class="table custom-table table-hover datatable">
            <thead>
                <tr>
                    <th>Photo</th>
                    <th>Name</th>
                    <th>Contact</th>
                    <th>Property Assigned</th>
                    <th>Verification Docs</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tenants as $tenant) : ?>
                    <tr>
                        <td>
                            <?php if ($tenant['profile_photo'] && file_exists(FCPATH . $tenant['profile_photo'])) : ?>
                                <img src="<?= base_url($tenant['profile_photo']) ?>" alt="<?= esc($tenant['name']) ?>" class="rounded-circle border" style="width: 38px; height: 38px; object-fit: cover;">
                            <?php else : ?>
                                <div class="bg-secondary bg-opacity-10 text-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; font-weight: 600; font-size: 13px;">
                                    <?= strtoupper(substr($tenant['name'], 0, 1)) ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="fw-bold text-dark fs-6"><?= esc($tenant['name']) ?></span>
                            <?php if ($tenant['user_id']) : ?>
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill ms-1" style="font-size: 10px;">Linked</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="font-size: 12.5px;"><i class="fa-solid fa-phone me-2 text-muted"></i><?= esc($tenant['mobile']) ?></div>
                            <div style="font-size: 12.5px;"><i class="fa-solid fa-envelope me-2 text-muted"></i><?= esc($tenant['email']) ?></div>
                        </td>
                        <td>
                            <?php if ($tenant['property_name']) : ?>
                                <span class="text-dark fw-500"><?= esc($tenant['property_name']) ?></span>
                                <small class="d-block text-muted" style="font-size: 11px;">Lease: <?= esc($tenant['agreement_number']) ?></small>
                            <?php else : ?>
                                <span class="text-muted" style="font-size: 12.5px;">Not Assigned</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="font-size: 11.5px;"><strong>Aadhaar:</strong> <?= $tenant['aadhaar_number'] ? esc($tenant['aadhaar_number']) : 'N/A' ?></div>
                            <div style="font-size: 11.5px;"><strong>PAN:</strong> <?= $tenant['pan_number'] ? esc($tenant['pan_number']) : 'N/A' ?></div>
                        </td>
                        <td class="text-end">
                            <div class="btn-actions justify-content-end">
                                <button class="btn btn-sm btn-view btn-outline-secondary" data-id="<?= $tenant['id'] ?>" title="View Details"><i class="fa-solid fa-eye text-info"></i></button>
                                <button class="btn btn-sm btn-edit btn-outline-secondary" data-id="<?= $tenant['id'] ?>" title="Edit"><i class="fa-solid fa-pen-to-square text-muted"></i></button>
                                <a href="<?= base_url('tenants/delete/' . $tenant['id']) ?>" class="btn btn-sm btn-outline-secondary" onclick="return confirm('Are you sure you want to delete this tenant profile? This will break any linked lease agreement.');"><i class="fa-solid fa-trash text-danger"></i></a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('modals') ?>
<!-- ADD TENANT MODAL -->
<div class="modal fade" id="addTenantModal" tabindex="-1" aria-labelledby="addTenantModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTenantModalLabel"><i class="fa-solid fa-user-plus text-primary me-2"></i>Add Tenant Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('tenants/store') ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" class="form-control" placeholder="John Doe" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mobile Number</label>
                            <input type="text" name="mobile" class="form-control" placeholder="Enter 10-digit number" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control" placeholder="john@example.com" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Link User Login (Optional)</label>
                            <select name="user_id" id="add_user_id" class="form-select">
                                <option value="">-- Don't Link / Setup Later --</option>
                                <?php foreach ($available_users as $user) : ?>
                                    <option value="<?= $user['id'] ?>"><?= esc($user['username']) ?> (<?= esc($user['email']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Auto Login Creation Switch -->
                    <div class="mb-3 p-3 rounded bg-light border border-secondary" id="add_auto_account_section">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" name="create_account" id="add_create_account" value="1">
                            <label class="form-check-label fw-bold text-dark" for="add_create_account">Auto-create login account</label>
                        </div>
                        <small class="text-muted">Username: <code>lowercase_name[rand]</code>, Password: <code>password123</code>.</small>
                    </div>

                    <!-- Assign Property Quick selection -->
                    <div class="mb-3">
                        <label class="form-label">Assign Property (Optional)</label>
                        <select name="property_id" class="form-select">
                            <option value="">-- Do Not Assign Unit Now --</option>
                            <?php foreach ($available_properties as $prop) : ?>
                                <option value="<?= $prop['id'] ?>"><?= esc($prop['name']) ?> (₹<?= number_format($prop['rent_amount'], 2) ?>/mo)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Aadhaar Number</label>
                            <input type="text" name="aadhaar_number" class="form-control" placeholder="12-digit UID">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">PAN Number</label>
                            <input type="text" name="pan_number" class="form-control" placeholder="10-digit Alphanumeric">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Permanent Address</label>
                        <textarea name="address" class="form-control" rows="2" required></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Profile Image</label>
                            <input type="file" name="profile_photo" class="form-control" accept="image/*">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Verification Document (PDF/Image)</label>
                            <input type="file" name="doc" class="form-control" accept=".pdf,image/*">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Tenant</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- EDIT TENANT MODAL -->
<div class="modal fade" id="editTenantModal" tabindex="-1" aria-labelledby="editTenantModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editTenantModalLabel"><i class="fa-solid fa-pen-to-square text-primary me-2"></i>Edit Tenant Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="editTenantForm" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" id="edit_name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mobile Number</label>
                            <input type="text" name="mobile" id="edit_mobile" class="form-control" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" id="edit_email" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Link User Account</label>
                            <select name="user_id" id="edit_user_id" class="form-select">
                                <option value="">-- Unlinked --</option>
                                <?php foreach ($all_tenant_users as $user) : ?>
                                    <option value="<?= $user['id'] ?>"><?= esc($user['username']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Aadhaar Number</label>
                            <input type="text" name="aadhaar_number" id="edit_aadhaar_number" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">PAN Number</label>
                            <input type="text" name="pan_number" id="edit_pan_number" class="form-control">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Permanent Address</label>
                        <textarea name="address" id="edit_address" class="form-control" rows="2" required></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Update Profile Image</label>
                            <input type="file" name="profile_photo" class="form-control" accept="image/*">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Update Verification Document copy</label>
                            <input type="file" name="doc" class="form-control" accept=".pdf,image/*">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Tenant</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- VIEW DETAILS MODAL -->
<div class="modal fade" id="viewTenantModal" tabindex="-1" aria-labelledby="viewTenantModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewTenantModalLabel"><i class="fa-solid fa-user text-primary me-2"></i>Tenant Profile Info</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div id="view_avatar_container" class="mb-4">
                    <!-- Populated dynamically -->
                </div>
                
                <h4 class="fw-bold text-dark mb-1" id="view_name"></h4>
                <p class="text-muted mb-4" id="view_email_display"></p>
                
                <div class="text-start bg-light border rounded p-4 mb-3" style="font-size: 13.5px;">
                    <div class="mb-2"><strong>Mobile:</strong> <span id="view_mobile"></span></div>
                    <div class="mb-2"><strong>Aadhaar Number:</strong> <span id="view_aadhaar"></span></div>
                    <div class="mb-2"><strong>PAN Number:</strong> <span id="view_pan"></span></div>
                    <div class="mb-2"><strong>Permanent Address:</strong> <span id="view_address"></span></div>
                </div>

                <div class="text-start" id="view_document_section">
                    <!-- Populated dynamically -->
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

        // Auto hide account creation switch if user selected
        $('#add_user_id').change(function() {
            if ($(this).val()) {
                $('#add_create_account').prop('checked', false);
                $('#add_auto_account_section').slideUp();
            } else {
                $('#add_auto_account_section').slideDown();
            }
        });

        // View Details Modal
        $('.btn-view').click(function() {
            var id = $(this).data('id');
            $.ajax({
                url: ajaxUrl + 'tenants/details/' + id,
                type: 'GET',
                success: function(res) {
                    if (res.status === 'success') {
                        var t = res.data;
                        $('#view_name').text(t.name);
                        $('#view_email_display').text(t.email);
                        $('#view_mobile').text(t.mobile);
                        $('#view_aadhaar').text(t.aadhaar_number || 'Not Provided');
                        $('#view_pan').text(t.pan_number || 'Not Provided');
                        $('#view_address').text(t.address);

                        // Profile Photo
                        var photoHtml = '';
                        if (t.profile_photo) {
                            photoHtml = '<img src="' + baseUrl + t.profile_photo + '" class="rounded-circle border border-3 border-primary shadow" style="width: 100px; height: 100px; object-fit: cover;">';
                        } else {
                            photoHtml = '<div class="bg-secondary bg-opacity-10 text-secondary rounded-circle border border-3 border-primary shadow mx-auto d-flex align-items-center justify-content-center" style="width: 100px; height: 100px; font-weight: 700; font-size: 28px;">' + t.name.charAt(0) + '</div>';
                        }
                        $('#view_avatar_container').html(photoHtml);

                        // Verification docs
                        var docsHtml = '';
                        if (t.doc_path) {
                            docsHtml = '<a href="' + baseUrl + t.doc_path + '" target="_blank" class="btn btn-outline-primary btn-sm w-100"><i class="fa-solid fa-file-pdf me-2"></i>Download Identity copy</a>';
                        } else {
                            docsHtml = '<div class="text-muted text-center" style="font-size: 13px;">No verification document copy.</div>';
                        }
                        $('#view_document_section').html(docsHtml);

                        $('#viewTenantModal').modal('show');
                    }
                }
            });
        });

        // Edit Modal
        $('.btn-edit').click(function() {
            var id = $(this).data('id');
            $('#editTenantForm').attr('action', ajaxUrl + 'tenants/update/' + id);
            $.ajax({
                url: ajaxUrl + 'tenants/details/' + id,
                type: 'GET',
                success: function(res) {
                    if (res.status === 'success') {
                        var t = res.data;
                        $('#edit_name').val(t.name);
                        $('#edit_mobile').val(t.mobile);
                        $('#edit_email').val(t.email);
                        $('#edit_user_id').val(t.user_id || '');
                        $('#edit_aadhaar_number').val(t.aadhaar_number);
                        $('#edit_pan_number').val(t.pan_number);
                        $('#edit_address').val(t.address);

                        $('#editTenantModal').modal('show');
                    }
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>
