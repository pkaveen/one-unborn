

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <h4 class="fw-bold text-primary mb-4">Edit Feasibility Status</h4>

    <div class="card shadow border-0 p-4">
        <form action="<?php echo e(route('feasibility.status.editSave', $record->id)); ?>" method="POST">
            <?php echo csrf_field(); ?>

            <div class="row mb-4">
                <div class="col-md-6">
                    <h6 class="fw-semibold text-muted">Client:</h6>
                    <p><?php echo e($record->feasibility->client->client_name ?? 'N/A'); ?></p>
                </div>
                <div class="col-md-6">
                    <h6 class="fw-semibold text-muted">Feasibility Type:</h6>
                    <p><?php echo e($record->feasibility->type_of_service ?? 'N/A'); ?></p>
                </div>
            </div>

            <hr>

            
            <h5 class="fw-bold text-primary mb-3">Vendor 1</h5>
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Name</label>
                    <input type="text" name="vendor1_name" class="form-control" value="<?php echo e($record->vendor1_name); ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">ARC</label>
                    <input type="text" name="vendor1_arc" class="form-control" value="<?php echo e($record->vendor1_arc); ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">OTC</label>
                    <input type="text" name="vendor1_otc" class="form-control" value="<?php echo e($record->vendor1_otc); ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Static IP Cost</label>
                    <input type="text" name="vendor1_static_ip_cost" class="form-control" value="<?php echo e($record->vendor1_static_ip_cost); ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Delivery Timeline</label>
                    <input type="text" name="vendor1_delivery_timeline" class="form-control" value="<?php echo e($record->vendor1_delivery_timeline); ?>">
                </div>
            </div>

            
            <h5 class="fw-bold text-primary mb-3">Vendor 2</h5>
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Name</label>
                    <input type="text" name="vendor2_name" class="form-control" value="<?php echo e($record->vendor2_name); ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">ARC</label>
                    <input type="text" name="vendor2_arc" class="form-control" value="<?php echo e($record->vendor2_arc); ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">OTC</label>
                    <input type="text" name="vendor2_otc" class="form-control" value="<?php echo e($record->vendor2_otc); ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Static IP Cost</label>
                    <input type="text" name="vendor2_static_ip_cost" class="form-control" value="<?php echo e($record->vendor2_static_ip_cost); ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Delivery Timeline</label>
                    <input type="text" name="vendor2_delivery_timeline" class="form-control" value="<?php echo e($record->vendor2_delivery_timeline); ?>">
                </div>
            </div>

            
            <h5 class="fw-bold text-primary mb-3">Vendor 3</h5>
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Name</label>
                    <input type="text" name="vendor3_name" class="form-control" value="<?php echo e($record->vendor3_name); ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">ARC</label>
                    <input type="text" name="vendor3_arc" class="form-control" value="<?php echo e($record->vendor3_arc); ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">OTC</label>
                    <input type="text" name="vendor3_otc" class="form-control" value="<?php echo e($record->vendor3_otc); ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Static IP Cost</label>
                    <input type="text" name="vendor3_static_ip_cost" class="form-control" value="<?php echo e($record->vendor3_static_ip_cost); ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Delivery Timeline</label>
                    <input type="text" name="vendor3_delivery_timeline" class="form-control" value="<?php echo e($record->vendor3_delivery_timeline); ?>">
                </div>
            </div>

            
            <h5 class="fw-bold text-primary mb-3">Vendor 4</h5>
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Name</label>
                    <input type="text" name="vendor4_name" class="form-control" value="<?php echo e($record->vendor4_name); ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">ARC</label>
                    <input type="text" name="vendor4_arc" class="form-control" value="<?php echo e($record->vendor4_arc); ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">OTC</label>
                    <input type="text" name="vendor4_otc" class="form-control" value="<?php echo e($record->vendor4_otc); ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Static IP Cost</label>
                    <input type="text" name="vendor4_static_ip_cost" class="form-control" value="<?php echo e($record->vendor4_static_ip_cost); ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Delivery Timeline</label>
                    <input type="text" name="vendor4_delivery_timeline" class="form-control" value="<?php echo e($record->vendor4_delivery_timeline); ?>">
                </div>
            </div>

            <hr>

            
            <div class="row">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Feasibility Status</label>
                    <select name="status" class="form-select" required>
                        <option value="Open" <?php echo e($record->status == 'Open' ? 'selected' : ''); ?>>Open</option>
                        <option value="InProgress" <?php echo e($record->status == 'InProgress' ? 'selected' : ''); ?>>In Progress</option>
                        <option value="Closed" <?php echo e($record->status == 'Closed' ? 'selected' : ''); ?>>Closed</option>
                    </select>
                </div>
            </div>

            <div class="mt-4 text-end">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Save Changes</button>
                <a href="<?php echo e(route('feasibility.status.index', 'Open')); ?>" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\new\multipleuserpage\resources\views/feasibility/feasibility_status/edit.blade.php ENDPATH**/ ?>