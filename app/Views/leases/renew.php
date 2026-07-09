<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-8 col-12">
        <div class="form-card">
            <h5 class="mb-2 fw-bold text-success"><i class="fa-solid fa-rotate text-success me-2 animate-spin"></i>Renew Lease Agreement</h5>
            <p class="text-muted mb-4">You are renewing the lease for <strong><?= esc($tenant['name']) ?></strong> at <strong><?= esc($property['name']) ?></strong>. The current lease (<?= esc($lease['agreement_number']) ?>) will be marked as expired.</p>
            
            <form action="<?= base_url('leases/attemptRenew/' . $lease['id']) ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="agreement_number" class="form-label">New Agreement Number</label>
                        <?php $suggestedCode = 'LEASE-' . date('Y') . '-' . mt_rand(100, 999); ?>
                        <input type="text" name="agreement_number" id="agreement_number" class="form-control" placeholder="e.g. LEASE-2026-004" value="<?= old('agreement_number', $suggestedCode) ?>" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="start_date" class="form-label">New Lease Start Date</label>
                        <!-- Start date is usually day after old end date -->
                        <?php $suggestedStart = date('Y-m-d', strtotime($lease['end_date'] . ' + 1 day')); ?>
                        <input type="date" name="start_date" id="start_date" class="form-control" value="<?= old('start_date', $suggestedStart) ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="end_date" class="form-label">New Lease End Date</label>
                        <?php $suggestedEnd = date('Y-m-d', strtotime($suggestedStart . ' + 1 year - 1 day')); ?>
                        <input type="date" name="end_date" id="end_date" class="form-control" value="<?= old('end_date', $suggestedEnd) ?>" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="monthly_rent" class="form-label">New Monthly Rent (INR)</label>
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
                    <label for="doc" class="form-label">Signed Renewal Agreement Document (Optional)</label>
                    <input type="file" name="doc" id="doc" class="form-control" accept=".pdf,image/*">
                    <span class="text-muted" style="font-size: 12px;">Leave blank to reuse the current lease document. Supported: PDF, JPG, PNG. Max 4MB.</span>
                </div>

                <div class="d-flex gap-3 justify-content-end">
                    <a href="<?= base_url('leases') ?>" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-success"><i class="fa-solid fa-circle-check me-2"></i>Renew Lease</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
