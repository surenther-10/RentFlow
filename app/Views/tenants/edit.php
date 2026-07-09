<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-8 col-12">
        <div class="form-card">
            <h5 class="mb-4 fw-bold"><i class="fa-solid fa-pen-to-square text-primary me-2"></i>Edit Tenant Profile</h5>
            
            <form action="<?= base_url('tenants/update/' . $tenant['id']) ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" name="name" id="name" class="form-control" value="<?= esc(old('name', $tenant['name'])) ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="mobile" class="form-label">Mobile Number</label>
                        <input type="text" name="mobile" id="mobile" class="form-control" value="<?= esc(old('mobile', $tenant['mobile'])) ?>" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" name="email" id="email" class="form-control" value="<?= esc(old('email', $tenant['email'])) ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="user_id" class="form-label">Link to User Account</label>
                        <select name="user_id" id="user_id" class="form-select">
                            <option value="">-- Unlinked / No Login Account --</option>
                            <?php foreach ($available_users as $user) : ?>
                                <option value="<?= $user['id'] ?>" <?= old('user_id', $tenant['user_id']) == $user['id'] ? 'selected' : '' ?>>
                                    <?= esc($user['username']) ?> (<?= esc($user['email']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="aadhaar_number" class="form-label">Aadhaar Number (12 digits)</label>
                        <input type="text" name="aadhaar_number" id="aadhaar_number" class="form-control" value="<?= esc(old('aadhaar_number', $tenant['aadhaar_number'])) ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="pan_number" class="form-label">PAN Number</label>
                        <input type="text" name="pan_number" id="pan_number" class="form-control" value="<?= esc(old('pan_number', $tenant['pan_number'])) ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="address" class="form-label">Permanent Address</label>
                    <textarea name="address" id="address" class="form-control" rows="3" required><?= esc(old('address', $tenant['address'])) ?></textarea>
                </div>

                <div class="mb-4">
                    <label for="doc" class="form-label">Update Verification Document</label>
                    <?php if ($tenant['doc_path']) : ?>
                        <div class="mb-3">
                            <span class="d-block text-muted mb-2" style="font-size: 13px;">Current Document:</span>
                            <a href="<?= base_url($tenant['doc_path']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary">
                                <i class="fa-solid fa-file-pdf me-2"></i>View Uploaded Document
                            </a>
                        </div>
                    <?php endif; ?>
                    <input type="file" name="doc" id="doc" class="form-control" accept=".pdf,image/*">
                    <span class="text-muted" style="font-size: 12px;">Leave blank to keep existing document. Supported: PDF, JPG, PNG. Max 4MB.</span>
                </div>

                <div class="d-flex gap-3 justify-content-end">
                    <a href="<?= base_url('tenants') ?>" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Tenant Profile</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
