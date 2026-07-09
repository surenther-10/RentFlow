<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-8 col-12">
        <div class="form-card">
            <h5 class="mb-4 fw-bold"><i class="fa-solid fa-plus text-primary me-2"></i>Add New Property</h5>
            
            <form action="<?= base_url('properties/store') ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                
                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label for="name" class="form-label">Property Name / Title</label>
                        <input type="text" name="name" id="name" class="form-control" placeholder="e.g. Sunset Apartments Block B Flat 402" value="<?= old('name') ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="type" class="form-label">Property Type</label>
                        <select name="type" id="type" class="form-select" required>
                            <option value="Apartment" <?= old('type') === 'Apartment' ? 'selected' : '' ?>>Apartment</option>
                            <option value="House" <?= old('type') === 'House' ? 'selected' : '' ?>>House</option>
                            <option value="Condo" <?= old('type') === 'Condo' ? 'selected' : '' ?>>Condo</option>
                            <option value="Commercial" <?= old('type') === 'Commercial' ? 'selected' : '' ?>>Commercial Space</option>
                            <option value="Room" <?= old('type') === 'Room' ? 'selected' : '' ?>>Single Room</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="address" class="form-label">Full Address</label>
                    <textarea name="address" id="address" class="form-control" rows="3" placeholder="Enter physical street address" required><?= old('address') ?></textarea>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="rent_amount" class="form-label">Monthly Rent Amount (INR)</label>
                        <div class="input-group">
                            <span class="input-group-text">₹</span>
                            <input type="number" name="rent_amount" id="rent_amount" class="form-control" placeholder="15000" step="0.01" value="<?= old('rent_amount') ?>" required>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="rooms" class="form-label">Number of Rooms (BHK)</label>
                        <input type="number" name="rooms" id="rooms" class="form-control" placeholder="2" value="<?= old('rooms') ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="availability_status" class="form-label">Initial Status</label>
                        <select name="availability_status" id="availability_status" class="form-select" required>
                            <option value="available" <?= old('availability_status') === 'available' ? 'selected' : '' ?>>Available</option>
                            <option value="rented" <?= old('availability_status') === 'rented' ? 'selected' : '' ?>>Rented</option>
                            <option value="maintenance" <?= old('availability_status') === 'maintenance' ? 'selected' : '' ?>>Under Maintenance</option>
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="image" class="form-label">Property Image</label>
                    <input type="file" name="image" id="image" class="form-control" accept="image/*">
                    <span class="text-muted" style="font-size: 12px;">Supported formats: JPG, JPEG, PNG, WEBP. Max size 2MB.</span>
                </div>

                <div class="d-flex gap-3 justify-content-end">
                    <a href="<?= base_url('properties') ?>" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Property</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
