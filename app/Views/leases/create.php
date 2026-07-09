<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-8 col-12">
        <div class="form-card">
            <h5 class="mb-4 fw-bold"><i class="fa-solid fa-plus text-primary me-2"></i>Create Lease Agreement</h5>
            
            <form action="<?= base_url('leases/store') ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="agreement_number" class="form-label">Agreement Number / Code</label>
                        <?php $suggestedCode = 'LEASE-' . date('Y') . '-' . mt_rand(100, 999); ?>
                        <input type="text" name="agreement_number" id="agreement_number" class="form-control" placeholder="e.g. LEASE-2026-003" value="<?= old('agreement_number', $suggestedCode) ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">Agreement Status</label>
                        <select name="status" id="status" class="form-select" required>
                            <option value="active" <?= old('status') === 'active' ? 'selected' : '' ?>>Active (Occupied)</option>
                            <option value="expired" <?= old('status') === 'expired' ? 'selected' : '' ?>>Expired</option>
                            <option value="terminated" <?= old('status') === 'terminated' ? 'selected' : '' ?>>Terminated</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="property_id" class="form-label">Select Available Property</label>
                        <select name="property_id" id="property_id" class="form-select" required>
                            <option value="">-- Choose Property --</option>
                            <?php foreach ($properties as $property) : ?>
                                <option value="<?= $property['id'] ?>" data-rent="<?= $property['rent_amount'] ?>" <?= old('property_id') == $property['id'] ? 'selected' : '' ?>>
                                    <?= esc($property['name']) ?> (₹<?= number_format($property['rent_amount'], 2) ?>/mo)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="tenant_id" class="form-label">Select Tenant Profile</label>
                        <select name="tenant_id" id="tenant_id" class="form-select" required>
                            <option value="">-- Choose Tenant --</option>
                            <?php foreach ($tenants as $tenant) : ?>
                                <option value="<?= $tenant['id'] ?>" <?= old('tenant_id') == $tenant['id'] ? 'selected' : '' ?>>
                                    <?= esc($tenant['name']) ?> (<?= esc($tenant['mobile']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="start_date" class="form-label">Lease Start Date</label>
                        <input type="date" name="start_date" id="start_date" class="form-control" value="<?= old('start_date', date('Y-m-d')) ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="end_date" class="form-label">Lease End Date</label>
                        <?php $oneYearLater = date('Y-m-d', strtotime('+1 year -1 day')); ?>
                        <input type="date" name="end_date" id="end_date" class="form-control" value="<?= old('end_date', $oneYearLater) ?>" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="monthly_rent" class="form-label">Monthly Rent Amount (INR)</label>
                        <div class="input-group">
                            <span class="input-group-text">₹</span>
                            <input type="number" name="monthly_rent" id="monthly_rent" class="form-control" placeholder="0" step="0.01" value="<?= old('monthly_rent') ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="security_deposit" class="form-label">Security Deposit (INR)</label>
                        <div class="input-group">
                            <span class="input-group-text">₹</span>
                            <input type="number" name="security_deposit" id="security_deposit" class="form-control" placeholder="0" step="0.01" value="<?= old('security_deposit') ?>" required>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="doc" class="form-label">Signed Agreement Document (PDF / Copy)</label>
                    <input type="file" name="doc" id="doc" class="form-control" accept=".pdf,image/*">
                    <span class="text-muted" style="font-size: 12px;">Supported: PDF, JPG, PNG. Max size 4MB.</span>
                </div>

                <div class="d-flex gap-3 justify-content-end">
                    <a href="<?= base_url('leases') ?>" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Create Agreement</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
    $(document).ready(function() {
        // Auto fill monthly rent based on selected property
        $('#property_id').change(function() {
            var selectedOption = $(this).find('option:selected');
            var rentAmount = selectedOption.data('rent');
            if (rentAmount) {
                $('#monthly_rent').val(rentAmount);
                // Pre-fill deposit as 2x monthly rent (common real estate default)
                $('#security_deposit').val(parseFloat(rentAmount) * 2);
            }
        });
    });
</script>
<?= $this->endSection() ?>
