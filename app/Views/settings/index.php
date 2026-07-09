<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-lg-8 col-12">
        <h5 class="fw-bold mb-4 text-dark"><i class="fa-solid fa-sliders text-primary me-2"></i>Global Platform Settings</h5>
        
        <div class="form-card">
            <form action="<?= base_url('admin/settings/update') ?>" method="POST">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label for="site_name" class="form-label">SaaS Site / Application Name</label>
                    <input type="text" name="site_name" id="site_name" class="form-control" value="<?= esc($settings['site_name'] ?? 'RentFlow SaaS') ?>" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="currency" class="form-label">Default Currency Symbol / Code</label>
                        <select name="currency" id="currency" class="form-select" required>
                            <option value="INR" <?= ($settings['currency'] ?? '') === 'INR' ? 'selected' : '' ?>>INR (₹)</option>
                            <option value="USD" <?= ($settings['currency'] ?? '') === 'USD' ? 'selected' : '' ?>>USD ($)</option>
                            <option value="EUR" <?= ($settings['currency'] ?? '') === 'EUR' ? 'selected' : '' ?>>EUR (€)</option>
                            <option value="GBP" <?= ($settings['currency'] ?? '') === 'GBP' ? 'selected' : '' ?>>GBP (£)</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="admin_email" class="form-label">Platform Admin Contact Email</label>
                        <input type="email" name="admin_email" id="admin_email" class="form-control" value="<?= esc($settings['admin_email'] ?? 'admin@rental.com') ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="contact_phone" class="form-label">Support Contact Number</label>
                    <input type="text" name="contact_phone" id="contact_phone" class="form-control" value="<?= esc($settings['contact_phone'] ?? '9876543210') ?>" required>
                </div>

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-primary px-4"><i class="fa-solid fa-floppy-disk me-2"></i>Update Config</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
