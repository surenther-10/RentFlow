<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold m-0 text-dark"><i class="fa-solid fa-users-gear text-primary me-2"></i>User Accounts Control</h5>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addUserModal">
        <i class="fa-solid fa-plus me-2"></i>Add User
    </button>
</div>

<!-- Table Card -->
<div class="custom-table-card">
    <div class="table-responsive">
        <table class="table custom-table table-hover datatable">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Email Address</th>
                    <th>Assigned Role</th>
                    <th>Account Status</th>
                    <th>Created At</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user) : ?>
                    <tr>
                        <td class="fw-bold text-dark">
                            <div class="d-flex align-items-center gap-2.5">
                                <?php if (!empty($user['profile_photo']) && file_exists(FCPATH . $user['profile_photo'])) : ?>
                                    <img src="<?= base_url($user['profile_photo']) ?>" alt="avatar" class="rounded-circle border shadow-sm" style="width: 32px; height: 32px; object-fit: cover;">
                                <?php else : ?>
                                    <div class="bg-secondary bg-opacity-10 text-secondary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 11px;">
                                        <?= strtoupper(substr($user['username'], 0, 2)) ?>
                                    </div>
                                <?php endif; ?>
                                <span><?= esc($user['username']) ?></span>
                            </div>
                        </td>
                        <td><?= esc($user['email']) ?></td>
                        <td>
                            <span class="badge bg-primary bg-opacity-10 text-primary text-capitalize px-2 py-1" style="font-size: 11.5px;">
                                <?= esc($user['role_name'] ?? $user['role']) ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-<?= $user['status'] === 'active' ? 'success' : 'danger' ?> bg-opacity-10 text-<?= $user['status'] === 'active' ? 'success' : 'danger' ?> rounded-pill px-2.5 py-1" style="font-size: 11px;">
                                <?= ucfirst(esc($user['status'])) ?>
                            </span>
                        </td>
                        <td class="text-muted" style="font-size: 12.5px;"><?= date('d M Y h:i A', strtotime($user['created_at'])) ?></td>
                        <td class="text-end">
                            <div class="btn-actions justify-content-end">
                                <button class="btn btn-sm btn-outline-secondary btn-edit" data-id="<?= $user['id'] ?>" title="Edit User"><i class="fa-solid fa-user-pen"></i></button>
                                <?php if ($user['id'] != session()->get('id')) : ?>
                                    <a href="<?= base_url('admin/users/delete/' . $user['id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this user account permanently?');" title="Delete"><i class="fa-solid fa-trash"></i></a>
                                <?php endif; ?>
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
<!-- ADD USER MODAL -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUserModalLabel"><i class="fa-solid fa-user-plus text-primary me-2"></i>Add User Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('admin/users/store') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="At least 6 characters" required>
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">System Role</label>
                            <select name="role_id" class="form-select" required>
                                <?php foreach ($roles as $role) : ?>
                                    <option value="<?= $role['id'] ?>"><?= esc($role['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Account</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- EDIT USER MODAL -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel"><i class="fa-solid fa-user-pen text-primary me-2"></i>Edit User Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="editUserForm">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" id="edit_username" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" id="edit_email" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Update Password (Leave blank to keep current)</label>
                        <input type="password" name="password" class="form-control" placeholder="At least 6 characters">
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">System Role</label>
                            <select name="role_id" id="edit_role_id" class="form-select" required>
                                <?php foreach ($roles as $role) : ?>
                                    <option value="<?= $role['id'] ?>"><?= esc($role['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" id="edit_status" class="form-select" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Account</button>
                </div>
            </form>
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

        $('.btn-edit').click(function() {
            var id = $(this).data('id');
            $('#editUserForm').attr('action', ajaxUrl + 'admin/users/update/' + id);
            $.ajax({
                url: ajaxUrl + 'admin/users/details/' + id,
                type: 'GET',
                success: function(res) {
                    if (res.status === 'success') {
                        var u = res.data;
                        $('#edit_username').val(u.username);
                        $('#edit_email').val(u.email);
                        $('#edit_role_id').val(u.role_id);
                        $('#edit_status').val(u.status);

                        $('#editUserModal').modal('show');
                    }
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>
