<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-8 col-12">
        <div class="form-card">
            <h5 class="mb-4 fw-bold"><i class="fa-solid fa-pen-to-square text-primary me-2"></i>Edit Lease Agreement</h5>
            
            <form action="<?= base_url('leases/update/' . $lease['id']) ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="agreement_number" class="form-label">Agreement Number / Code</label>
                        <input type="text" name="agreement_number" id="agreement_number" class="form-control" value="<?= esc(old('agreement_number', $lease['agreement_number'])) ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">Agreement Status</label>
                        <select name="status" id="status" class="form-select" required>
                            <option value="active" <?= old('status', $lease['status']) === 'active' ? 'selected' : '' ?>>Active (Occupied)</option>
                            <option value="expired" <?= old('status', $lease['status']) === 'expired' ? 'selected' : '' ?>>Expired</option>
                            <option value="terminated" <?= old('status', $lease['status']) === 'terminated' ? 'selected' : '' ?>>Terminated</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="property_id" class="form-label">Select Property</label>
                        <select name="property_id" id="property_id" class="form-select" required>
                            <?php foreach ($properties as $property) : ?>
                                <option value="<?= $property['id'] ?>" <?= old('property_id', $lease['property_id']) == $property['id'] ? 'selected' : '' ?>>
                                    <?= esc($property['name']) ?> (₹<?= number_format($property['rent_amount'], 2) ?>/mo) - <?= ucfirst($property['availability_status']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="tenant_id" class="form-label">Select Tenant</label>
                        <select name="tenant_id" id="tenant_id" class="form-select" required>
                            <?php foreach ($tenants as $tenant) : ?>
                                <option value="<?= $tenant['id'] ?>" <?= old('tenant_id', $lease['tenant_id']) == $tenant['id'] ? 'selected' : '' ?>>
                                    <?= esc($tenant['name']) ?> (<?= esc($tenant['mobile']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="start_date" class="form-label">Lease Start Date</label>
                        <input type="date" name="start_date" id="start_date" class="form-control" value="<?= esc(old('start_date', $lease['start_date'])) ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="end_date" class="form-label">Lease End Date</label>
                        <input type="date" name="end_date" id="end_date" class="form-control" value="<?= esc(old('end_date', $lease['end_date'])) ?>" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="monthly_rent" class="form-label">Monthly Rent Amount (INR)</label>
                        <div class="input-group">
                            <span class="input-group-text">₹</span>
                            <input type="number" name="monthly_rent" id="monthly_rent" class="form-control" step="0.01" value="<?= esc(old('monthly_rent', $lease['monthly_rent'])) ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="security_deposit" class="form-label">Security Deposit (INR)</label>
                        <div class="input-group">
                            <span class="input-group-text">₹</span>
                            <input type="number" name="security_deposit" id="security_deposit" class="form-control" step="0.01" value="<?= esc(old('security_deposit', $lease['security_deposit'])) ?>" required>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="doc" class="form-label">Update Agreement Document</label>
                    <?php if ($lease['doc_path']) : ?>
                        <div class="mb-3">
                            <span class="d-block text-muted mb-2" style="font-size: 13px;">Current Agreement File:</span>
                            <a href="<?= base_url($lease['doc_path']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary">
                                <i class="fa-solid fa-file-pdf me-2"></i>View Signed File
                            </a>
                        </div>
                    <?php endif; ?>
                    <input type="file" name="doc" id="doc" class="form-control" accept=".pdf,image/*">
                    <span class="text-muted" style="font-size: 12px;">Leave blank to keep existing document. Supported: PDF, JPG, PNG. Max 4MB.</span>
                </div>

                <div class="d-flex gap-3 justify-content-end">
                    <a href="<?= base_url('leases') ?>" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Agreement</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
