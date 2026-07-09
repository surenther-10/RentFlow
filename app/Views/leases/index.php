<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold m-0 text-dark"><i class="fa-solid fa-file-contract text-primary me-2"></i>Lease Agreements</h5>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addLeaseModal">
        <i class="fa-solid fa-plus me-2"></i>Create Lease
    </button>
</div>

<!-- Table Card -->
<div class="custom-table-card">
    <div class="table-responsive">
        <table class="table custom-table table-hover datatable">
            <thead>
                <tr>
                    <th>Agreement Info</th>
                    <th>Property Unit</th>
                    <th>Tenant Details</th>
                    <th>Financials</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($leases as $lease) : ?>
                    <tr>
                        <td>
                            <span class="fw-bold text-dark"><?= esc($lease['agreement_number']) ?></span>
                            <small class="d-block text-muted" style="font-size: 11px;">
                                <?= date('d M Y', strtotime($lease['start_date'])) ?> to <?= date('d M Y', strtotime($lease['end_date'])) ?>
                            </small>
                        </td>
                        <td>
                            <span class="text-dark fw-500"><?= esc($lease['property_name']) ?></span>
                            <small class="d-block text-muted" style="font-size: 11px;"><?= esc($lease['property_type']) ?></small>
                        </td>
                        <td>
                            <span class="text-dark fw-500"><?= esc($lease['tenant_name']) ?></span>
                            <small class="d-block text-muted" style="font-size: 11px;"><?= esc($lease['tenant_mobile']) ?></small>
                        </td>
                        <td>
                            <div style="font-size: 12.5px;"><strong>Rent:</strong> <span class="text-success fw-bold">₹<?= number_format($lease['monthly_rent'], 2) ?>/mo</span></div>
                            <div style="font-size: 11.5px; color: #64748b;"><strong>Deposit:</strong> ₹<?= number_format($lease['security_deposit'], 2) ?></div>
                        </td>
                        <td>
                            <span class="badge-status <?= esc($lease['status']) ?>">
                                <?= ucfirst(esc($lease['status'])) ?>
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="btn-actions justify-content-end">
                                <button class="btn btn-sm btn-outline-secondary btn-renew-trigger" data-id="<?= $lease['id'] ?>" title="Renew"><i class="fa-solid fa-rotate text-success"></i></button>
                                <button class="btn btn-sm btn-outline-secondary btn-view" data-id="<?= $lease['id'] ?>" title="View"><i class="fa-solid fa-eye text-info"></i></button>
                                <button class="btn btn-sm btn-outline-secondary btn-edit" data-id="<?= $lease['id'] ?>" title="Edit"><i class="fa-solid fa-pen-to-square text-muted"></i></button>
                                <a href="<?= base_url('leases/delete/' . $lease['id']) ?>" class="btn btn-sm btn-outline-secondary" onclick="return confirm('Are you sure you want to delete this lease agreement? Property status will return to available.');" title="Delete"><i class="fa-solid fa-trash text-danger"></i></a>
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
<!-- ADD LEASE MODAL -->
<div class="modal fade" id="addLeaseModal" tabindex="-1" aria-labelledby="addLeaseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addLeaseModalLabel"><i class="fa-solid fa-file-contract text-primary me-2"></i>Create Lease Agreement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('leases/store') ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Agreement Number / Code</label>
                            <?php $suggestedCode = 'LEASE-' . date('Y') . '-' . mt_rand(100, 999); ?>
                            <input type="text" name="agreement_number" class="form-control" value="<?= old('agreement_number', $suggestedCode) ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Agreement Status</label>
                            <select name="status" class="form-select" required>
                                <option value="active">Active (Occupied)</option>
                                <option value="expired">Expired</option>
                                <option value="terminated">Terminated</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Select Available Property</label>
                            <select name="property_id" id="add_property_id" class="form-select" required>
                                <option value="">-- Choose Unit --</option>
                                <?php foreach ($available_properties as $prop) : ?>
                                    <option value="<?= $prop['id'] ?>" data-rent="<?= $prop['rent_amount'] ?>">
                                        <?= esc($prop['name']) ?> (₹<?= number_format($prop['rent_amount'], 2) ?>/mo)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Select Tenant Profile</label>
                            <select name="tenant_id" class="form-select" required>
                                <option value="">-- Choose Tenant --</option>
                                <?php foreach ($tenants as $ten) : ?>
                                    <option value="<?= $ten['id'] ?>"><?= esc($ten['name']) ?> (<?= esc($ten['mobile']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Lease Start Date</label>
                            <input type="date" name="start_date" id="add_start_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Lease End Date</label>
                            <?php $oneYear = date('Y-m-d', strtotime('+1 year -1 day')); ?>
                            <input type="date" name="end_date" id="add_end_date" class="form-control" value="<?= $oneYear ?>" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Monthly Rent Amount (INR)</label>
                            <input type="number" name="monthly_rent" id="add_monthly_rent" class="form-control" step="0.01" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Security Deposit (INR)</label>
                            <input type="number" name="security_deposit" id="add_security_deposit" class="form-control" step="0.01" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Signed Agreement Document (PDF / Scan)</label>
                        <input type="file" name="doc" class="form-control" accept=".pdf,image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Create Lease</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- EDIT LEASE MODAL -->
<div class="modal fade" id="editLeaseModal" tabindex="-1" aria-labelledby="editLeaseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editLeaseModalLabel"><i class="fa-solid fa-pen-to-square text-primary me-2"></i>Edit Lease Agreement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="editLeaseForm" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Agreement Number / Code</label>
                            <input type="text" name="agreement_number" id="edit_agreement_number" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Agreement Status</label>
                            <select name="status" id="edit_status" class="form-select" required>
                                <option value="active">Active (Occupied)</option>
                                <option value="expired">Expired</option>
                                <option value="terminated">Terminated</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Select Property</label>
                            <select name="property_id" id="edit_property_id" class="form-select" required>
                                <?php foreach ($properties as $prop) : ?>
                                    <option value="<?= $prop['id'] ?>"><?= esc($prop['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Select Tenant Profile</label>
                            <select name="tenant_id" id="edit_tenant_id" class="form-select" required>
                                <?php foreach ($tenants as $ten) : ?>
                                    <option value="<?= $ten['id'] ?>"><?= esc($ten['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Lease Start Date</label>
                            <input type="date" name="start_date" id="edit_start_date" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Lease End Date</label>
                            <input type="date" name="end_date" id="edit_end_date" class="form-control" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Monthly Rent Amount (INR)</label>
                            <input type="number" name="monthly_rent" id="edit_monthly_rent" class="form-control" step="0.01" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Security Deposit (INR)</label>
                            <input type="number" name="security_deposit" id="edit_security_deposit" class="form-control" step="0.01" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Update Agreement Document (PDF / Scan)</label>
                        <input type="file" name="doc" class="form-control" accept=".pdf,image/*">
                        <div id="edit_doc_preview" class="mt-2"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Lease</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- RENEW LEASE MODAL -->
<div class="modal fade" id="renewLeaseModal" tabindex="-1" aria-labelledby="renewLeaseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="renewLeaseModalLabel"><i class="fa-solid fa-rotate text-success me-2"></i>Renew Lease Agreement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="renewLeaseForm" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="alert alert-info border-start border-3 border-info bg-info bg-opacity-10 py-3 mb-4 text-dark" style="font-size: 13.5px;">
                        You are creating a renewal agreement for tenant <strong id="renew_tenant_name"></strong> at <strong id="renew_property_name"></strong>. The current active lease will automatically be archived as Expired.
                    </div>

                    <div class="mb-3">
                        <label class="form-label">New Agreement Number</label>
                        <input type="text" name="agreement_number" id="renew_agreement_number" class="form-control" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">New Lease Start Date</label>
                            <input type="date" name="start_date" id="renew_start_date" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">New Lease End Date</label>
                            <input type="date" name="end_date" id="renew_end_date" class="form-control" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">New Monthly Rent (INR)</label>
                            <input type="number" name="monthly_rent" id="renew_monthly_rent" class="form-control" step="0.01" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">New Security Deposit (INR)</label>
                            <input type="number" name="security_deposit" id="renew_security_deposit" class="form-control" step="0.01" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">New Signed Agreement Document (Optional)</label>
                        <input type="file" name="doc" class="form-control" accept=".pdf,image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Renew Contract</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- VIEW LEASE DETAILS MODAL -->
<div class="modal fade" id="viewLeaseModal" tabindex="-1" aria-labelledby="viewLeaseModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewLeaseModalLabel"><i class="fa-solid fa-file-contract text-primary me-2"></i>Lease Contract Info</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-start" style="font-size: 13.5px;">
                <div class="text-center mb-4">
                    <h3 class="fw-bold text-dark mb-1" id="view_lease_no"></h3>
                    <span class="badge-status" id="view_status"></span>
                </div>

                <div class="bg-light border rounded p-4 mb-4">
                    <div class="mb-2"><strong>Tenant:</strong> <span id="view_tenant"></span></div>
                    <div class="mb-2"><strong>Rented Property:</strong> <span id="view_property"></span></div>
                    <div class="mb-2"><strong>Property Configuration:</strong> <span id="view_rooms"></span> BHK (<span id="view_type"></span>)</div>
                    <div class="mb-2"><strong>Start Date:</strong> <span id="view_start_date"></span></div>
                    <div class="mb-2"><strong>End Date:</strong> <span id="view_end_date"></span></div>
                    <div class="mb-2"><strong>Monthly Rent:</strong> <span class="text-success fw-bold">₹<span id="view_rent"></span></span></div>
                    <div class="mb-2"><strong>Security Deposit:</strong> ₹<span id="view_deposit"></span></div>
                </div>

                <div id="view_doc_section">
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

        // Auto rent filler on create lease
        $('#add_property_id').change(function() {
            var selectedOption = $(this).find('option:selected');
            var rent = selectedOption.data('rent');
            if (rent) {
                $('#add_monthly_rent').val(rent);
                $('#add_security_deposit').val(parseFloat(rent) * 2);
            }
        });

        // View Lease details
        $('.btn-view').click(function() {
            var id = $(this).data('id');
            $.ajax({
                url: ajaxUrl + 'leases/details/' + id,
                type: 'GET',
                success: function(res) {
                    if (res.status === 'success') {
                        var l = res.data;
                        $('#view_lease_no').text(l.agreement_number);
                        $('#view_tenant').text(l.tenant_name + ' (' + l.tenant_mobile + ')');
                        $('#view_property').text(l.property_name + ' &mdash; ' + l.property_address);
                        $('#view_rooms').text(l.rooms);
                        $('#view_type').text(l.property_type);
                        $('#view_start_date').text(l.start_date);
                        $('#view_end_date').text(l.end_date);
                        $('#view_rent').text(parseFloat(l.monthly_rent).toLocaleString('en-IN', {minimumFractionDigits: 2}));
                        $('#view_deposit').text(parseFloat(l.security_deposit).toLocaleString('en-IN', {minimumFractionDigits: 2}));

                        $('#view_status').text(l.status.toUpperCase()).removeClass().addClass('badge-status ' + l.status);

                        // Signed document preview
                        var docHtml = '';
                        if (l.doc_path) {
                            docHtml = '<a href="' + baseUrl + l.doc_path + '" target="_blank" class="btn btn-outline-primary btn-sm w-100"><i class="fa-solid fa-file-pdf me-2"></i>Download Lease Document (PDF)</a>';
                        } else {
                            docHtml = '<div class="text-muted text-center" style="font-size: 13px;">No signed contract copy.</div>';
                        }
                        $('#view_doc_section').html(docHtml);

                        $('#viewLeaseModal').modal('show');
                    }
                }
            });
        });

        // Edit Lease details
        $('.btn-edit').click(function() {
            var id = $(this).data('id');
            $('#editLeaseForm').attr('action', ajaxUrl + 'leases/update/' + id);
            $.ajax({
                url: ajaxUrl + 'leases/details/' + id,
                type: 'GET',
                success: function(res) {
                    if (res.status === 'success') {
                        var l = res.data;
                        $('#edit_agreement_number').val(l.agreement_number);
                        $('#edit_status').val(l.status);
                        $('#edit_property_id').val(l.property_id);
                        $('#edit_tenant_id').val(l.tenant_id);
                        $('#edit_start_date').val(l.start_date);
                        $('#edit_end_date').val(l.end_date);
                        $('#edit_monthly_rent').val(l.monthly_rent);
                        $('#edit_security_deposit').val(l.security_deposit);

                        var previewHtml = '';
                        if (l.doc_path) {
                            previewHtml = '<a href="' + baseUrl + l.doc_path + '" target="_blank" class="text-primary text-decoration-none" style="font-size: 12px;"><i class="fa-solid fa-file-pdf me-2"></i>View Signed Doc</a>';
                        }
                        $('#edit_doc_preview').html(previewHtml);

                        $('#editLeaseModal').modal('show');
                    }
                }
            });
        });

        // Renew Lease trigger
        $('.btn-renew-trigger').click(function() {
            var id = $(this).data('id');
            $('#renewLeaseForm').attr('action', ajaxUrl + 'leases/attemptRenew/' + id);
            $.ajax({
                url: ajaxUrl + 'leases/details/' + id,
                type: 'GET',
                success: function(res) {
                    if (res.status === 'success') {
                        var l = res.data;
                        $('#renew_tenant_name').text(l.tenant_name);
                        $('#renew_property_name').text(l.property_name);

                        // Prefill details
                        var splitCode = l.agreement_number.split('-');
                        var nextCode = 'LEASE-' + new Date().getFullYear() + '-' + Math.floor(100 + Math.random() * 900);
                        $('#renew_agreement_number').val(nextCode);

                        // New start date is the day after the old end date
                        var oldEnd = new Date(l.end_date);
                        oldEnd.setDate(oldEnd.getDate() + 1);
                        var startVal = oldEnd.toISOString().split('T')[0];
                        $('#renew_start_date').val(startVal);

                        // New end date is 1 year minus 1 day from the new start date
                        var oldEndNew = new Date(startVal);
                        oldEndNew.setFullYear(oldEndNew.getFullYear() + 1);
                        oldEndNew.setDate(oldEndNew.getDate() - 1);
                        var endVal = oldEndNew.toISOString().split('T')[0];
                        $('#renew_end_date').val(endVal);

                        $('#renew_monthly_rent').val(l.monthly_rent);
                        $('#renew_security_deposit').val(l.security_deposit);

                        $('#renewLeaseModal').modal('show');
                    }
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>
