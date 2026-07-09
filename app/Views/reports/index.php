<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4 no-print">
    <h5 class="fw-bold m-0 text-dark"><i class="fa-solid fa-chart-pie text-primary me-2"></i>System Analytics & Reports</h5>
    <button class="btn btn-outline-secondary btn-sm bg-white" onclick="window.print();"><i class="fa-solid fa-print me-2 text-info"></i>Print Analytics Page</button>
</div>

<!-- Export Section -->
<div class="form-card mb-5 bg-white border border-secondary border-opacity-10">
    <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-file-export text-primary me-2"></i>Download Operational Audits (CSV / Excel)</h6>
    <p class="text-muted mb-4" style="font-size: 13.5px;">Export complete relational database ledger tables to Excel natively.</p>
    
    <div class="row g-3">
        <div class="col-lg col-md-4 col-6">
            <a href="<?= base_url('reports/exportRent') ?>" class="btn btn-outline-secondary w-100 py-3 d-flex flex-column align-items-center justify-content-center gap-2 text-dark">
                <i class="fa-solid fa-file-invoice-dollar fs-3 text-success"></i>
                <span class="fw-bold" style="font-size: 12.5px;">Rent Collections</span>
            </a>
        </div>
        <div class="col-lg col-md-4 col-6">
            <a href="<?= base_url('reports/exportOccupancy') ?>" class="btn btn-outline-secondary w-100 py-3 d-flex flex-column align-items-center justify-content-center gap-2 text-dark">
                <i class="fa-solid fa-building-circle-check fs-3 text-info"></i>
                <span class="fw-bold" style="font-size: 12.5px;">Occupancy List</span>
            </a>
        </div>
        <div class="col-lg col-md-4 col-6">
            <a href="<?= base_url('reports/exportTenants') ?>" class="btn btn-outline-secondary w-100 py-3 d-flex flex-column align-items-center justify-content-center gap-2 text-dark">
                <i class="fa-solid fa-users-viewfinder fs-3 text-warning"></i>
                <span class="fw-bold" style="font-size: 12.5px;">Tenants Profile</span>
            </a>
        </div>
        <div class="col-lg col-md-4 col-6">
            <a href="<?= base_url('reports/exportMaintenance') ?>" class="btn btn-outline-secondary w-100 py-3 d-flex flex-column align-items-center justify-content-center gap-2 text-dark">
                <i class="fa-solid fa-toolbox fs-3 text-danger"></i>
                <span class="fw-bold" style="font-size: 12.5px;">Service tickets</span>
            </a>
        </div>
        <div class="col-lg col-md-4 col-6">
            <a href="<?= base_url('reports/exportRevenue') ?>" class="btn btn-outline-secondary w-100 py-3 d-flex flex-column align-items-center justify-content-center gap-2 text-dark">
                <i class="fa-solid fa-chart-line-up fs-3 text-primary"></i>
                <span class="fw-bold" style="font-size: 12.5px;">Revenue curve</span>
            </a>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Occupancy Analytics Card -->
    <div class="col-lg-6 col-12">
        <div class="form-card h-100">
            <h6 class="fw-bold text-dark mb-4"><i class="fa-solid fa-house-chimney-user text-primary me-2"></i>Occupancy Status</h6>
            
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h2 class="fw-bold text-dark m-0"><?= esc($occupancy_rate) ?>%</h2>
                    <span class="text-muted" style="font-size: 12px;">Current Occupancy Rate</span>
                </div>
                <div class="fs-1 text-primary opacity-20">
                    <i class="fa-solid fa-people-roof"></i>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="progress mb-4" style="height: 8px; border-radius: 9999px; background-color: #f1f5f9;">
                <div class="progress-bar bg-success" role="progressbar" style="width: <?= esc($occupancy_rate) ?>%" aria-valuenow="<?= esc($occupancy_rate) ?>" aria-valuemin="0" aria-valuemax="100"></div>
                <div class="progress-bar bg-warning" role="progressbar" style="width: <?= $total_properties > 0 ? esc(($maintenance_count / $total_properties) * 100) : 0 ?>%" aria-valuenow="<?= $maintenance_count ?>" aria-valuemin="0" aria-valuemax="100"></div>
            </div>

            <div class="row text-center mt-3 g-2">
                <div class="col-4 border-end">
                    <span class="text-success fw-bold d-block fs-6"><?= esc($rented_count) ?></span>
                    <small class="text-muted" style="font-size: 11px;">Rented Units</small>
                </div>
                <div class="col-4 border-end">
                    <span class="text-danger fw-bold d-block fs-6"><?= esc($available_count) ?></span>
                    <small class="text-muted" style="font-size: 11px;">Vacant Units</small>
                </div>
                <div class="col-4">
                    <span class="text-warning fw-bold d-block fs-6"><?= esc($maintenance_count) ?></span>
                    <small class="text-muted" style="font-size: 11px;">Under Service</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Analytics Card -->
    <div class="col-lg-6 col-12">
        <div class="form-card h-100">
            <h6 class="fw-bold text-dark mb-4"><i class="fa-solid fa-indian-rupee-sign text-success me-2"></i>Rent Financial Summary</h6>
            
            <div class="row g-3 mb-4 text-center">
                <div class="col-4">
                    <div class="p-2.5 bg-light rounded border border-success border-opacity-10">
                        <small class="text-muted d-block" style="font-size: 11px;">Collected</small>
                        <strong class="text-success fs-6">₹<?= number_format($total_collected, 2) ?></strong>
                    </div>
                </div>
                <div class="col-4">
                    <div class="p-2.5 bg-light rounded border border-warning border-opacity-10">
                        <small class="text-muted d-block" style="font-size: 11px;">Pending Dues</small>
                        <strong class="text-warning fs-6">₹<?= number_format($total_pending, 2) ?></strong>
                    </div>
                </div>
                <div class="col-4">
                    <div class="p-2.5 bg-light rounded border border-danger border-opacity-10">
                        <small class="text-muted d-block" style="font-size: 11px;">Overdues</small>
                        <strong class="text-danger fs-6">₹<?= number_format($total_overdue, 2) ?></strong>
                    </div>
                </div>
            </div>

            <div class="p-2.5 bg-light rounded text-center border" style="font-size: 12.5px;">
                <span class="text-muted">Estimated Annual Potential: </span>
                <strong class="text-dark">₹<?= number_format(($total_collected + $total_pending + $total_overdue) * 12, 2) ?></strong>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Maintenance Ticket Metrics -->
    <div class="col-lg-6 col-12">
        <div class="form-card">
            <h6 class="fw-bold text-dark mb-4"><i class="fa-solid fa-screwdriver-wrench text-danger me-2"></i>Service Ticket Audits</h6>
            
            <div class="row g-2 mb-4 text-center">
                <div class="col-3">
                    <span class="fw-bold text-danger fs-5"><?= esc($ticket_open) ?></span>
                    <small class="text-muted d-block" style="font-size: 11px;">Open</small>
                </div>
                <div class="col-3">
                    <span class="fw-bold text-warning fs-5"><?= esc($ticket_inprogress) ?></span>
                    <small class="text-muted d-block" style="font-size: 11px;">In Progress</small>
                </div>
                <div class="col-3">
                    <span class="fw-bold text-success fs-5"><?= esc($ticket_completed) ?></span>
                    <small class="text-muted d-block" style="font-size: 11px;">Completed</small>
                </div>
                <div class="col-3">
                    <span class="fw-bold text-secondary fs-5"><?= esc($ticket_closed) ?></span>
                    <small class="text-muted d-block" style="font-size: 11px;">Closed</small>
                </div>
            </div>

            <div class="p-2.5 bg-light rounded text-center border" style="font-size: 12.5px;">
                <span class="text-muted">Total maintenance requests logged: </span>
                <strong class="text-dark"><?= esc($ticket_total) ?></strong>
            </div>
        </div>
    </div>

    <!-- Revenue collection history list -->
    <div class="col-lg-6 col-12">
        <div class="custom-table-card">
            <div class="custom-table-header">
                <h6 class="m-0 fw-bold text-dark"><i class="fa-solid fa-chart-line text-primary me-2"></i>Monthly Revenue Collection List</h6>
            </div>
            <div class="table-responsive">
                <table class="table custom-table">
                    <thead>
                        <tr>
                            <th>Month Period</th>
                            <th>Transactions</th>
                            <th class="text-end">Total Collected</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($monthly_collections)) : ?>
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">No monthly collections recorded.</td>
                            </tr>
                        <?php else : ?>
                            <?php foreach ($monthly_collections as $row) : ?>
                                <tr>
                                    <td class="fw-bold text-dark"><?= date('F Y', strtotime($row['month'] . '-01')) ?></td>
                                    <td><?= esc($row['transaction_count']) ?> collections</td>
                                    <td class="text-end fw-bold text-success">₹<?= number_format($row['total_amount'], 2) ?></td>
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
