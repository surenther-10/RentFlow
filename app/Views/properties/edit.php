<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-8 col-12">
        <div class="form-card">
            <h5 class="mb-4 fw-bold"><i class="fa-solid fa-pen-to-square text-primary me-2"></i>Edit Property Details</h5>
            
            <form action="<?= base_url('properties/update/' . $property['id']) ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                
                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label for="name" class="form-label">Property Name / Title</label>
                        <input type="text" name="name" id="name" class="form-control" value="<?= esc(old('name', $property['name'])) ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="type" class="form-label">Property Type</label>
                        <select name="type" id="type" class="form-select" required>
                            <option value="Apartment" <?= old('type', $property['type']) === 'Apartment' ? 'selected' : '' ?>>Apartment</option>
                            <option value="House" <?= old('type', $property['type']) === 'House' ? 'selected' : '' ?>>House</option>
                            <option value="Condo" <?= old('type', $property['type']) === 'Condo' ? 'selected' : '' ?>>Condo</option>
                            <option value="Commercial" <?= old('type', $property['type']) === 'Commercial' ? 'selected' : '' ?>>Commercial Space</option>
                            <option value="Room" <?= old('type', $property['type']) === 'Room' ? 'selected' : '' ?>>Single Room</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="address" class="form-label">Full Address</label>
                    <textarea name="address" id="address" class="form-control" rows="3" required><?= esc(old('address', $property['address'])) ?></textarea>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="rent_amount" class="form-label">Monthly Rent Amount (INR)</label>
                        <div class="input-group">
                            <span class="input-group-text">₹</span>
                            <input type="number" name="rent_amount" id="rent_amount" class="form-control" step="0.01" value="<?= esc(old('rent_amount', $property['rent_amount'])) ?>" required>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="rooms" class="form-label">Number of Rooms (BHK)</label>
                        <input type="number" name="rooms" id="rooms" class="form-control" value="<?= esc(old('rooms', $property['rooms'])) ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="availability_status" class="form-label">Availability Status</label>
                        <select name="availability_status" id="availability_status" class="form-select" required>
                            <option value="available" <?= old('availability_status', $property['availability_status']) === 'available' ? 'selected' : '' ?>>Available</option>
                            <option value="rented" <?= old('availability_status', $property['availability_status']) === 'rented' ? 'selected' : '' ?>>Rented</option>
                            <option value="maintenance" <?= old('availability_status', $property['availability_status']) === 'maintenance' ? 'selected' : '' ?>>Under Maintenance</option>
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="image" class="form-label">Update Property Image</label>
                    <?php if ($property['image']) : ?>
                        <div class="mb-3">
                            <span class="d-block text-muted mb-2" style="font-size: 13px;">Current Image:</span>
                            <img src="<?= base_url($property['image']) ?>" alt="Property Image" style="max-height: 120px; border-radius: 8px;" class="border">
                        </div>
                    <?php endif; ?>
                    <input type="file" name="image" id="image" class="form-control" accept="image/*">
                    <span class="text-muted" style="font-size: 12px;">Leave blank to keep current image. Supported formats: JPG, JPEG, PNG, WEBP. Max 2MB.</span>
                </div>

                <div class="d-flex gap-3 justify-content-end">
                    <a href="<?= base_url('properties') ?>" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Property</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
