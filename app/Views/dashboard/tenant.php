<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<!-- Alerts Section -->
<?php if (!empty($alerts)) : ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-warning border-start border-3 border-warning bg-warning bg-opacity-10 py-3 shadow-sm" role="alert">
                <div class="fw-bold text-warning mb-2"><i class="fa-solid fa-bell me-2"></i>Action Required</div>
                <ul class="mb-0 ps-3 text-dark" style="font-size: 13px;">
                    <?php foreach ($alerts as $alert) : ?>
                        <li class="mb-1.5"><?= $alert['message'] ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Quick Tenant Stats -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="stat-card primary">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p>Monthly Rent</p>
                    <h3>₹<?= $active_lease ? number_format($active_lease['monthly_rent'], 2) : '0.00' ?></h3>
                </div>
                <div class="icon-box">
                    <i class="fa-solid fa-indian-rupee-sign"></i>
                </div>
            </div>
            <div class="mt-2 text-muted" style="font-size: 11.5px;">
                Due by 5th of each month
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card success">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p>Lease End Date</p>
                    <h3><?= $active_lease ? date('d M Y', strtotime($active_lease['end_date'])) : 'N/A' ?></h3>
                </div>
                <div class="icon-box">
                    <i class="fa-solid fa-calendar-days"></i>
                </div>
            </div>
            <div class="mt-2 text-muted" style="font-size: 11.5px;">
                Agreement: <?= $active_lease ? esc($active_lease['agreement_number']) : 'No active agreement' ?>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card warning">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p>Security Deposit</p>
                    <h3>₹<?= $active_lease ? number_format($active_lease['security_deposit'], 2) : '0.00' ?></h3>
                </div>
                <div class="icon-box">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
            </div>
            <div class="mt-2 text-muted" style="font-size: 11.5px;">
                Refundable upon lease termination
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Property Info -->
    <div class="col-lg-12">
        <div class="form-card">
            <h6 class="fw-bold text-dark mb-4"><i class="fa-solid fa-house-user text-primary me-2"></i>Your Rented Property Unit</h6>
            <?php if ($active_lease) : ?>
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="fw-bold text-dark mb-2"><?= esc($active_lease['property_name']) ?></h4>
                        <p class="text-muted mb-3"><i class="fa-solid fa-location-dot me-2 text-danger"></i><?= esc($active_lease['property_address']) ?></p>
                        <div class="d-flex flex-wrap gap-4 text-muted" style="font-size: 13.5px;">
                            <div><i class="fa-solid fa-door-open me-2 text-primary"></i><strong>Property Type:</strong> <?= esc($active_lease['property_type']) ?></div>
                            <div><i class="fa-solid fa-file-contract me-2 text-success"></i><strong>Agreement No:</strong> <?= esc($active_lease['agreement_number']) ?></div>
                            <div><i class="fa-solid fa-calendar me-2 text-warning"></i><strong>Lease Period:</strong> <?= date('d M Y', strtotime($active_lease['start_date'])) ?> to <?= date('d M Y', strtotime($active_lease['end_date'])) ?></div>
                        </div>
                    </div>
                    <div class="col-md-4 text-md-end mt-4 mt-md-0">
                        <a href="<?= base_url('maintenance') ?>" class="btn btn-primary btn-sm"><i class="fa-solid fa-screwdriver-wrench me-2"></i>Raise Service Ticket</a>
                    </div>
                </div>
            <?php else : ?>
                <div class="text-center py-4">
                    <p class="text-muted m-0">You currently do not have any active lease agreement assigned. Please contact your property manager.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Rent History -->
    <div class="col-lg-6 col-12">
        <div class="custom-table-card">
            <div class="custom-table-header d-flex justify-content-between align-items-center">
                <h6 class="m-0 fw-bold text-dark"><i class="fa-solid fa-indian-rupee-sign text-success me-2"></i>Recent Rent Receipts</h6>
                <a href="<?= base_url('rent') ?>" class="btn btn-sm btn-outline-secondary py-0.5 px-2" style="font-size: 11.5px;">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table custom-table table-hover">
                    <thead>
                        <tr>
                            <th>Receipt No</th>
                            <th>Amount Paid</th>
                            <th>Date</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($payments)) : ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">No rent receipts found.</td>
                            </tr>
                        <?php else : ?>
                            <?php foreach ($payments as $payment) : ?>
                                <tr>
                                    <td class="fw-bold text-dark"><?= esc($payment['receipt_number']) ?></td>
                                    <td class="fw-bold text-success">₹<?= number_format($payment['amount'], 2) ?></td>
                                    <td><?= date('d M Y', strtotime($payment['payment_date'])) ?></td>
                                    <td class="text-end">
                                        <a href="<?= base_url('rent/receipt/' . $payment['id']) ?>" class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-eye me-1 text-info"></i> Receipt</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Maintenance Tickets -->
    <div class="col-lg-6 col-12">
        <div class="custom-table-card">
            <div class="custom-table-header d-flex justify-content-between align-items-center">
                <h6 class="m-0 fw-bold text-dark"><i class="fa-solid fa-screwdriver-wrench text-danger me-2"></i>Service Requests</h6>
                <a href="<?= base_url('maintenance') ?>" class="btn btn-sm btn-outline-secondary py-0.5 px-2" style="font-size: 11.5px;">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table custom-table table-hover">
                    <thead>
                        <tr>
                            <th>Ticket ID</th>
                            <th>Title</th>
                            <th class="text-end">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($tickets)) : ?>
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">No tickets raised yet.</td>
                            </tr>
                        <?php else : ?>
                            <?php foreach ($tickets as $ticket) : ?>
                                <tr>
                                    <td class="fw-bold text-dark">TKT-<?= str_pad($ticket['id'], 5, '0', STR_PAD_LEFT) ?></td>
                                    <td><?= esc($ticket['title']) ?></td>
                                    <td class="text-end">
                                        <span class="badge-status <?= strtolower(str_replace(' ', '', $ticket['status'])) ?>">
                                            <?= esc($ticket['status']) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
