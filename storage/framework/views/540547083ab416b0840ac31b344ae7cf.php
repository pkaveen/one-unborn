

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <h4 class="fw-bold text-primary mb-3">View Feasibility Status</h4>

    <div class="card shadow border-0 p-4">
        <div class="row mb-4">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Client Name</label>
                <input type="text" class="form-control" value="<?php echo e($record->feasibility->client->client_name ?? '-'); ?>" readonly>
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold">Type of Service</label>
                <input type="text" class="form-control" value="<?php echo e($record->feasibility->type_of_service ?? '-'); ?>" readonly>
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold">Status</label>
                <input type="text" class="form-control" value="<?php echo e($record->status); ?>" readonly>
            </div>
        </div>

        <hr>

        
        <h5 class="fw-bold text-primary mt-3 mb-2">Vendor 1</h5>
        <div class="row g-3 mb-4">
            <div class="col-md-3"><label class="form-label fw-semibold">Name</label>
                <input type="text" class="form-control" value="<?php echo e($record->vendor1_name ?? '-'); ?>" readonly>
            </div>
            <div class="col-md-2"><label class="form-label fw-semibold">ARC</label>
                <input type="text" class="form-control" value="<?php echo e($record->vendor1_arc ?? '-'); ?>" readonly>
            </div>
            <div class="col-md-2"><label class="form-label fw-semibold">OTC</label>
                <input type="text" class="form-control" value="<?php echo e($record->vendor1_otc ?? '-'); ?>" readonly>
            </div>
            <div class="col-md-2"><label class="form-label fw-semibold">Static IP Cost</label>
                <input type="text" class="form-control" value="<?php echo e($record->vendor1_static_ip_cost ?? '-'); ?>" readonly>
            </div>
            <div class="col-md-3"><label class="form-label fw-semibold">Delivery Timeline</label>
                <input type="text" class="form-control" value="<?php echo e($record->vendor1_delivery_timeline ?? '-'); ?>" readonly>
            </div>
        </div>

        
        <h5 class="fw-bold text-primary mt-3 mb-2">Vendor 2</h5>
        <div class="row g-3 mb-4">
            <div class="col-md-3"><input type="text" class="form-control" value="<?php echo e($record->vendor2_name ?? '-'); ?>" readonly></div>
            <div class="col-md-2"><input type="text" class="form-control" value="<?php echo e($record->vendor2_arc ?? '-'); ?>" readonly></div>
            <div class="col-md-2"><input type="text" class="form-control" value="<?php echo e($record->vendor2_otc ?? '-'); ?>" readonly></div>
            <div class="col-md-2"><input type="text" class="form-control" value="<?php echo e($record->vendor2_static_ip_cost ?? '-'); ?>" readonly></div>
            <div class="col-md-3"><input type="text" class="form-control" value="<?php echo e($record->vendor2_delivery_timeline ?? '-'); ?>" readonly></div>
        </div>

        
        <h5 class="fw-bold text-primary mt-3 mb-2">Vendor 3</h5>
        <div class="row g-3 mb-4">
            <div class="col-md-3"><input type="text" class="form-control" value="<?php echo e($record->vendor3_name ?? '-'); ?>" readonly></div>
            <div class="col-md-2"><input type="text" class="form-control" value="<?php echo e($record->vendor3_arc ?? '-'); ?>" readonly></div>
            <div class="col-md-2"><input type="text" class="form-control" value="<?php echo e($record->vendor3_otc ?? '-'); ?>" readonly></div>
            <div class="col-md-2"><input type="text" class="form-control" value="<?php echo e($record->vendor3_static_ip_cost ?? '-'); ?>" readonly></div>
            <div class="col-md-3"><input type="text" class="form-control" value="<?php echo e($record->vendor3_delivery_timeline ?? '-'); ?>" readonly></div>
        </div>

        
        <h5 class="fw-bold text-primary mt-3 mb-2">Vendor 4</h5>
        <div class="row g-3 mb-4">
            <div class="col-md-3"><input type="text" class="form-control" value="<?php echo e($record->vendor4_name ?? '-'); ?>" readonly></div>
            <div class="col-md-2"><input type="text" class="form-control" value="<?php echo e($record->vendor4_arc ?? '-'); ?>" readonly></div>
            <div class="col-md-2"><input type="text" class="form-control" value="<?php echo e($record->vendor4_otc ?? '-'); ?>" readonly></div>
            <div class="col-md-2"><input type="text" class="form-control" value="<?php echo e($record->vendor4_static_ip_cost ?? '-'); ?>" readonly></div>
            <div class="col-md-3"><input type="text" class="form-control" value="<?php echo e($record->vendor4_delivery_timeline ?? '-'); ?>" readonly></div>
        </div>

        <div class="mt-4 text-end">
            <a href="<?php echo e(route('feasibility.status.edit', $record->id)); ?>" class="btn btn-primary">
                <i class="bi bi-pencil"></i> Edit
            </a>
            <a href="<?php echo e(route('feasibility.status.index', 'Open')); ?>" class="btn btn-secondary">
                Back
            </a>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\new\multipleuserpage\resources\views/feasibility/feasibility_status/show.blade.php ENDPATH**/ ?>