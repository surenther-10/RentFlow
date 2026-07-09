<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold m-0 text-dark"><i class="fa-solid fa-screwdriver-wrench text-primary me-2"></i>Maintenance Tickets Log</h5>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addTicketModal">
        <i class="fa-solid fa-plus me-2"></i>Raise Ticket
    </button>
</div>

<!-- Table Card -->
<div class="custom-table-card">
    <div class="table-responsive">
        <table class="table custom-table table-hover datatable">
            <thead>
                <tr>
                    <th>Ticket ID</th>
                    <th>Property Unit</th>
                    <th>Renter / Tenant</th>
                    <th>Description Summary</th>
                    <th>Technician</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tickets as $ticket) : ?>
                    <tr>
                        <td class="fw-bold text-dark">TKT-<?= str_pad($ticket['id'], 5, '0', STR_PAD_LEFT) ?></td>
                        <td><?= esc($ticket['property_name']) ?></td>
                        <td><?= esc($ticket['tenant_name']) ?></td>
                        <td>
                            <span class="text-dark fw-500"><?= esc($ticket['title']) ?></span>
                            <small class="d-block text-muted" style="font-size: 11px;"><?= date('d M Y h:i A', strtotime($ticket['created_at'])) ?></small>
                        </td>
                        <td>
                            <span class="text-muted"><?= $ticket['assigned_technician'] ? esc($ticket['assigned_technician']) : 'Unassigned' ?></span>
                        </td>
                        <td>
                            <span class="badge-status <?= strtolower(str_replace(' ', '', $ticket['status'])) ?>">
                                <?= esc($ticket['status']) ?>
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="btn-actions justify-content-end">
                                <button class="btn btn-sm btn-outline-secondary btn-view" data-id="<?= $ticket['id'] ?>" title="Open Ticket Board"><i class="fa-solid fa-comments text-info"></i> Details & Comments</button>
                                <?php if (session()->get('role') === 'admin' || session()->get('role') === 'owner') : ?>
                                    <a href="<?= base_url('maintenance/delete/' . $ticket['id']) ?>" class="btn btn-sm btn-outline-secondary" onclick="return confirm('Delete this maintenance record permanently?');" title="Delete"><i class="fa-solid fa-trash text-danger"></i></a>
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
<!-- ADD TICKET MODAL -->
<div class="modal fade" id="addTicketModal" tabindex="-1" aria-labelledby="addTicketModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTicketModalLabel"><i class="fa-solid fa-screwdriver-wrench text-primary me-2"></i>Log Service Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('maintenance/store') ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <?php if (session()->get('role') === 'tenant') : ?>
                        <!-- Tenant fields -->
                        <div class="mb-3">
                            <label class="form-label">Rented Unit</label>
                            <?php if ($tenant_property) : ?>
                                <input type="text" class="form-control" value="<?= esc($tenant_property['name']) ?>" readonly>
                                <input type="hidden" name="property_id" value="<?= $tenant_property['id'] ?>">
                            <?php else : ?>
                                <div class="alert alert-warning border-start border-3 border-warning bg-warning bg-opacity-10 py-2" style="font-size: 13px;">No active lease agreement found. You cannot log tickets without active units.</div>
                                <input type="hidden" name="property_id" value="">
                            <?php endif; ?>
                        </div>
                    <?php else : ?>
                        <!-- Admin fields -->
                        <div class="mb-3">
                            <label class="form-label">Select Property Unit</label>
                            <select name="property_id" class="form-select" required>
                                <option value="">-- Choose Unit --</option>
                                <?php foreach ($properties as $prop) : ?>
                                    <option value="<?= $prop['id'] ?>"><?= esc($prop['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Select Tenant</label>
                            <select name="tenant_id" class="form-select" required>
                                <option value="">-- Choose Tenant --</option>
                                <?php
                                $db = \Config\Database::connect();
                                $allTenants = $db->table('tenants')->get()->getResultArray();
                                foreach ($allTenants as $t) : ?>
                                    <option value="<?= $t['id'] ?>"><?= esc($t['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label">Issue Title Summary</label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. Electrical sparks in kitchen" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Detailed Description</label>
                        <textarea name="description" class="form-control" rows="4" placeholder="Describe issue severity..." required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Upload Photo/Document attachment</label>
                        <input type="file" name="attachment" class="form-control" accept="image/*,.pdf">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" <?= (session()->get('role') === 'tenant' && empty($tenant_property)) ? 'disabled' : '' ?>>Log Ticket</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- VIEW DETAILS & COMMENTS BOARD MODAL -->
<div class="modal fade" id="ticketBoardModal" tabindex="-1" aria-labelledby="ticketBoardModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ticketBoardModalLabel"><i class="fa-solid fa-comments text-primary me-2"></i>Service Ticket Board</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-4">
                    <!-- Ticket Details Column -->
                    <div class="col-md-6 col-12 border-end">
                        <div class="p-3 bg-light border rounded mb-4">
                            <span class="badge bg-primary text-uppercase mb-2" id="board_ticket_id"></span>
                            <h4 class="fw-bold text-dark mb-2" id="board_title"></h4>
                            <div class="text-muted mb-2" style="font-size: 13px;"><i class="fa-solid fa-building me-2"></i>Unit: <span id="board_property"></span></div>
                            <div class="text-muted mb-2" style="font-size: 13px;"><i class="fa-solid fa-user me-2"></i>Tenant: <span id="board_tenant"></span></div>
                            <div class="text-muted mb-2" style="font-size: 13px;"><i class="fa-solid fa-calendar me-2"></i>Logged: <span id="board_date"></span></div>
                        </div>

                        <div class="mb-4">
                            <h6 class="fw-bold text-dark">Issue Description</h6>
                            <p class="text-muted" id="board_description" style="font-size: 13.5px;"></p>
                        </div>

                        <!-- Attachment link -->
                        <div class="mb-4" id="board_attachment_section"></div>

                        <hr class="border-secondary opacity-25">

                        <!-- Admin Management Forms -->
                        <?php if (session()->get('role') === 'admin' || session()->get('role') === 'owner') : ?>
                            <div class="mt-4">
                                <h6 class="fw-bold text-dark mb-3">Management Options</h6>
                                
                                <!-- Assign Technician -->
                                <form action="" method="POST" id="assignTechForm" class="mb-3">
                                    <?= csrf_field() ?>
                                    <div class="input-group">
                                        <input type="text" name="assigned_technician" id="board_tech_input" class="form-control form-control-sm" placeholder="Technician Name" required>
                                        <button type="submit" class="btn btn-sm btn-primary">Assign</button>
                                    </div>
                                </form>

                                <!-- Change Status -->
                                <form action="" method="POST" id="changeStatusForm">
                                    <?= csrf_field() ?>
                                    <div class="input-group">
                                        <select name="status" id="board_status_select" class="form-select form-select-sm" required>
                                            <option value="Open">Open</option>
                                            <option value="In Progress">In Progress</option>
                                            <option value="Completed">Completed</option>
                                            <option value="Closed">Closed</option>
                                        </select>
                                        <button type="submit" class="btn btn-sm btn-success">Update Status</button>
                                    </div>
                                </form>
                            </div>
                        <?php else : ?>
                            <div class="p-3 bg-light border rounded mt-4" style="font-size: 13px;">
                                <div class="mb-2"><strong>Assigned Tech:</strong> <span id="board_tech_display"></span></div>
                                <div><strong>Ticket Status:</strong> <span class="badge-status" id="board_status_display"></span></div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Comments timeline Column -->
                    <div class="col-md-6 col-12 d-flex flex-column" style="max-height: 500px;">
                        <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-list-ul me-2 text-primary"></i>Conversation History</h6>
                        
                        <div class="comment-timeline flex-grow-1" id="comments_feed">
                            <!-- Populated dynamically via JS -->
                        </div>

                        <!-- Add Comment form -->
                        <form id="commentSubmitForm" class="mt-3">
                            <div class="input-group">
                                <input type="text" id="comment_input" class="form-control" placeholder="Type a message or update..." required>
                                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-paper-plane"></i></button>
                            </div>
                        </form>
                    </div>
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
        var currentTicketId = null;

        // View details and load timeline
        $('.btn-view').click(function() {
            var id = $(this).data('id');
            currentTicketId = id;
            
            // Set action paths for admin forms
            $('#assignTechForm').attr('action', ajaxUrl + 'maintenance/assign/' + id);
            $('#changeStatusForm').attr('action', ajaxUrl + 'maintenance/updateStatus/' + id);

            $.ajax({
                url: ajaxUrl + 'maintenance/details/' + id,
                type: 'GET',
                success: function(res) {
                    if (res.status === 'success') {
                        var t = res.data;
                        $('#board_ticket_id').text('TKT-' + String(t.id).padStart(5, '0'));
                        $('#board_title').text(t.title);
                        $('#board_property').text(t.property_name);
                        $('#board_tenant').text(t.tenant_name);
                        $('#board_date').text(t.created_at);
                        $('#board_description').text(t.description);

                        // Attachments copy
                        var attachHtml = '';
                        if (t.attachment_path) {
                            attachHtml = '<a href="' + baseUrl + t.attachment_path + '" target="_blank" class="btn btn-sm btn-outline-info w-100"><i class="fa-solid fa-paperclip me-2"></i>Download Attachment</a>';
                        } else {
                            attachHtml = '<div class="text-muted" style="font-size: 12px;"><i class="fa-solid fa-paperclip me-2"></i>No attachment.</div>';
                        }
                        $('#board_attachment_section').html(attachHtml);

                        // If admin
                        $('#board_tech_input').val(t.assigned_technician || '');
                        $('#board_status_select').val(t.status);

                        // If tenant
                        $('#board_tech_display').text(t.assigned_technician || 'None');
                        $('#board_status_display').text(t.status).removeClass().addClass('badge-status ' + t.status.toLowerCase().replace(' ', ''));

                        // Comments timeline
                        var commentsHtml = '';
                        if (t.comments.length === 0) {
                            commentsHtml = '<div class="text-center text-muted py-5" id="no_comments_msg">No comments logged.</div>';
                        } else {
                            t.comments.forEach(function(c) {
                                var badgeClass = c.role === 'admin' || c.role === 'owner' ? 'bg-danger bg-opacity-10 text-danger' : 'bg-primary bg-opacity-10 text-primary';
                                commentsHtml += '<div class="comment-card">' +
                                    '<div class="comment-meta">' +
                                        '<span><strong class="text-dark">' + c.username + '</strong> <span class="badge ' + badgeClass + ' ms-1" style="font-size: 9px;">' + c.role.toUpperCase() + '</span></span>' +
                                        '<span>' + c.created_at + '</span>' +
                                    '</div>' +
                                    '<p class="m-0 text-secondary" style="font-size: 13px;">' + c.comment + '</p>' +
                                    '</div>';
                            });
                        }
                        $('#comments_feed').html(commentsHtml);

                        var feed = $('#comments_feed');
                        feed.scrollTop(feed.prop("scrollHeight"));

                        $('#ticketBoardModal').modal('show');
                    }
                }
            });
        });

        // Submit comment via AJAX
        $('#commentSubmitForm').submit(function(e) {
            e.preventDefault();
            if (!currentTicketId) return;

            var commentText = $('#comment_input').val();
            $.ajax({
                url: ajaxUrl + 'maintenance/comment/' + currentTicketId,
                type: 'POST',
                data: {
                    comment: commentText,
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                },
                success: function(res) {
                    if (res.status === 'success') {
                        $('#comment_input').val('');
                        $('#no_comments_msg').remove();

                        var badgeClass = res.role === 'admin' || res.role === 'owner' ? 'bg-danger bg-opacity-10 text-danger' : 'bg-primary bg-opacity-10 text-primary';
                        var newCommentHtml = '<div class="comment-card">' +
                            '<div class="comment-meta">' +
                                '<span><strong class="text-dark">' + res.username + '</strong> <span class="badge ' + badgeClass + ' ms-1" style="font-size: 9px;">' + res.role.toUpperCase() + '</span></span>' +
                                '<span>' + res.created_at + '</span>' +
                            '</div>' +
                            '<p class="m-0 text-secondary" style="font-size: 13px;">' + res.comment + '</p>' +
                            '</div>';
                        
                        $('#comments_feed').append(newCommentHtml);
                        
                        var feed = $('#comments_feed');
                        feed.scrollTop(feed.prop("scrollHeight"));
                    } else {
                        alert(res.message);
                    }
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>
