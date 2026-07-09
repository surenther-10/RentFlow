<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-lg-8 col-12">
        <h5 class="fw-bold mb-4 text-dark"><i class="fa-solid fa-user-gear text-primary me-2"></i>My Profile Settings</h5>
        
        <div class="form-card">
            <form action="<?= base_url('profile') ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>

                <div class="text-center mb-4">
                    <!-- Current Avatar Preview -->
                    <div class="mb-3">
                        <?php 
                        $avatarPath = $user['profile_photo'] ?? null;
                        if (!empty($avatarPath) && file_exists(FCPATH . $avatarPath)) : ?>
                            <img src="<?= base_url($avatarPath) ?>" alt="Avatar" class="rounded-circle border border-3 border-primary shadow" style="width: 120px; height: 120px; object-fit: cover;">
                        <?php else : ?>
                            <div class="bg-secondary rounded-circle border border-3 border-primary shadow mx-auto d-flex align-items-center justify-content-center text-white" style="width: 120px; height: 120px; font-weight: 700; font-size: 36px; background: var(--primary-gradient) !important;">
                                <?= strtoupper(substr($user['username'], 0, 2)) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <h5 class="fw-bold text-dark mb-1"><?= esc($user['username']) ?></h5>
                    <span class="badge bg-primary text-uppercase"><?= esc($role) ?></span>
                </div>

                <h6 class="mb-3 text-uppercase border-bottom border-secondary border-opacity-25 pb-2" style="font-size: 11px; letter-spacing: 0.5px; color: #818cf8;">Account Credentials</h6>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" name="username" id="username" class="form-control" value="<?= old('username', $user['username']) ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" name="email" id="email" class="form-control" value="<?= old('email', $user['email']) ?>" required>
                    </div>
                </div>

                <?php if ($role === 'admin') : ?>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="profile_photo" class="form-label">Update Profile Photo</label>
                            <input type="file" name="profile_photo" id="profile_photo" class="form-control" accept="image/*">
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($role !== 'admin') : ?>
                    <h6 class="mt-4 mb-3 text-uppercase border-bottom border-secondary border-opacity-25 pb-2" style="font-size: 11px; letter-spacing: 0.5px; color: #818cf8;">Profile Information</h6>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" name="name" id="name" class="form-control" value="<?= old('name', $profile['name'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="mobile" class="form-label">Mobile Number</label>
                            <input type="text" name="mobile" id="mobile" class="form-control" value="<?= old('mobile', $profile['mobile'] ?? '') ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Permanent Address</label>
                        <textarea name="address" id="address" class="form-control" rows="3" required><?= old('address', $profile['address'] ?? '') ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="profile_photo" class="form-label">Update Profile Photo</label>
                            <input type="file" name="profile_photo" id="profile_photo" class="form-control" accept="image/*">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="doc" class="form-label">ID Proof Document (Optional)</label>
                            <input type="file" name="doc" id="doc" class="form-control" accept=".pdf,image/*">
                            <?php if (!empty($profile['doc_path'])) : ?>
                                <small class="d-block mt-2">
                                    <a href="<?= base_url($profile['doc_path']) ?>" target="_blank" class="text-primary text-decoration-none">
                                        <i class="fa-solid fa-file-pdf me-2"></i>Download Current ID Proof
                                    </a>
                                </small>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-primary px-4"><i class="fa-solid fa-floppy-disk me-2"></i>Save Settings</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
