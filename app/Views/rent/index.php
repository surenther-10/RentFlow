<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold m-0 text-dark"><i class="fa-solid fa-credit-card text-primary me-2"></i>Rent Collections</h5>
    <?php if (session()->get('role') === 'admin' || session()->get('role') === 'owner') : ?>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#recordPaymentModal">
            <i class="fa-solid fa-plus me-2"></i>Record Rent
        </button>
    <?php endif; ?>
</div>

<!-- Table Card -->
<div class="custom-table-card">
    <div class="table-responsive">
        <table class="table custom-table table-hover datatable">
            <thead>
                <tr>
                    <th>Receipt No</th>
                    <th>Property</th>
                    <th>Tenant</th>
                    <th>Amount Paid</th>
                    <th>Date</th>
                    <th>Method</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($payments as $payment) : ?>
                    <tr>
                        <td class="fw-bold text-dark">
                            <a href="<?= base_url('rent/receipt/' . $payment['id']) ?>" class="text-primary text-decoration-none">
                                <?= esc($payment['receipt_number']) ?>
                            </a>
                        </td>
                        <td>
                            <span class="text-dark fw-500"><?= esc($payment['property_name']) ?></span>
                        </td>
                        <td>
                            <span class="text-muted"><?= esc($payment['tenant_name']) ?></span>
                        </td>
                        <td class="fw-bold text-success">₹<?= number_format($payment['amount'], 2) ?></td>
                        <td><?= date('d M Y', strtotime($payment['payment_date'])) ?></td>
                        <td><?= esc($payment['payment_method']) ?></td>
                        <td>
                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2.5 py-1" style="font-size: 11px;">
                                <?= esc($payment['status']) ?>
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="btn-actions justify-content-end">
                                <a href="<?= base_url('rent/receipt/' . $payment['id']) ?>" class="btn btn-sm btn-outline-secondary" title="View Receipt"><i class="fa-solid fa-file-invoice text-info"></i></a>
                                <a href="<?= base_url('rent/download/' . $payment['id']) ?>" class="btn btn-sm btn-outline-secondary" title="Download PDF"><i class="fa-solid fa-file-pdf text-primary"></i></a>
                                <?php if (session()->get('role') === 'admin' || session()->get('role') === 'owner') : ?>
                                    <a href="<?= base_url('rent/delete/' . $payment['id']) ?>" class="btn btn-sm btn-outline-secondary" onclick="return confirm('Are you sure you want to delete this payment record?');" title="Delete"><i class="fa-solid fa-trash text-danger"></i></a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- RECORD PAYMENT MODAL -->
<?php if (session()->get('role') === 'admin' || session()->get('role') === 'owner') : ?>
<div class="modal fade" id="recordPaymentModal" tabindex="-1" aria-labelledby="recordPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="recordPaymentModalLabel"><i class="fa-solid fa-indian-rupee-sign text-primary me-2"></i>Record Rent Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('rent/store') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Select Active Lease Contract</label>
                        <select name="lease_id" id="add_lease_id" class="form-select" required>
                            <option value="">-- Choose Contract --</option>
                            <?php foreach ($active_leases as $l) : ?>
                                <option value="<?= $l['id'] ?>" data-rent="<?= $l['monthly_rent'] ?>">
                                    <?= esc($l['agreement_number']) ?> &mdash; <?= esc($l['tenant_name']) ?> (<?= esc($l['property_name']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Payment Amount (INR)</label>
                        <input type="number" name="amount" id="add_amount" class="form-control" step="0.01" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Payment Date</label>
                            <input type="date" name="payment_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Payment Method</label>
                            <select name="payment_method" class="form-select" required>
                                <option value="UPI">UPI</option>
                                <option value="Bank Transfer">Bank Transfer</option>
                                <option value="Cash">Cash</option>
                                <option value="Online Payment">Online Payment Gateway</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Payment Status</label>
                        <select name="status" class="form-select" required>
                            <option value="Paid">Paid</option>
                            <option value="Pending">Pending</option>
                            <option value="Overdue">Overdue</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="e.g. Rent for June 2026"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Record Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Auto fill rent amount based on selected lease contract
        $('#add_lease_id').change(function() {
            var selectedOption = $(this).find('option:selected');
            var rent = selectedOption.data('rent');
            if (rent) {
                $('#add_amount').val(rent);
            }
        });
    });
</script>
<?= $this->endSection() ?>
