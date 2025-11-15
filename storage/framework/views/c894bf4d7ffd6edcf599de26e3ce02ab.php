

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-hourglass-split me-2"></i>Open Deliverables</h5>
                    <span class="badge bg-light text-dark"><?php echo e($records->count()); ?> Records</span>
                </div>

                <div class="card-body">
                    <?php if($records->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Delivery ID</th>
                                        <th>Feasibility</th>
                                        <th>Client</th>
                                        <th>PO Number</th>
                                        <th>PO Date</th>
                                        <th>Site Address</th>
                                        <th>Link Type</th>
                                        <th>Speed</th>
                                        <th>Vendor</th>
                                        <th>Total Cost</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td>
                                                <span class="badge bg-info"><?php echo e($record->delivery_id); ?></span>
                                            </td>
                                            <td>
                                                <?php if($record->feasibility): ?>
                                                    <small class="text-muted"><?php echo e($record->feasibility->feasibility_request_id); ?></small>
                                                <?php else: ?>
                                                    <span class="text-muted">N/A</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if($record->feasibility && $record->feasibility->client): ?>
                                                    <strong><?php echo e($record->feasibility->client->client_name); ?></strong>
                                                <?php else: ?>
                                                    <span class="text-muted">No Client</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if($record->purchaseOrder): ?>
                                                    <span class="badge bg-success"><?php echo e($record->purchaseOrder->po_number); ?></span>
                                                <?php else: ?>
                                                    <span class="text-muted">No PO</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if($record->purchaseOrder): ?>
                                                    <small class="text-muted"><?php echo e($record->purchaseOrder->po_date->format('d-M-Y')); ?></small>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <small><?php echo e($record->site_address ?? 'Not specified'); ?></small>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary"><?php echo e($record->link_type ?? 'N/A'); ?></span>
                                            </td>
                                            <td>
                                                <?php echo e($record->speed_in_mbps ?? 'N/A'); ?> Mbps
                                            </td>
                                            <td>
                                                <?php echo e($record->vendor ?? 'Not assigned'); ?>

                                            </td>
                                            <td>
                                                <?php if($record->purchaseOrder): ?>
                                                    <?php
                                                        $totalCost = ($record->purchaseOrder->arc_per_link + $record->purchaseOrder->otc_per_link + $record->purchaseOrder->static_ip_cost_per_link) * $record->purchaseOrder->no_of_links;
                                                    ?>
                                                    <strong class="text-success">₹<?php echo e(number_format($totalCost, 2)); ?></strong>
                                                    <br><small class="text-muted"><?php echo e($record->purchaseOrder->no_of_links); ?> Links</small>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-warning text-dark">
                                                    <i class="bi bi-hourglass-split"></i> <?php echo e($record->status); ?>

                                                </span>
                                            </td>
                                            <td>
                                                <small class="text-muted"><?php echo e($record->created_at->format('M d, Y')); ?></small>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="<?php echo e(route('operations.deliverables.view', $record->id)); ?>" 
                                                       class="btn btn-outline-info btn-sm"
                                                       title="View Details">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="<?php echo e(route('operations.deliverables.edit', $record->id)); ?>" 
                                                       class="btn btn-outline-primary btn-sm"
                                                       title="Edit Deliverable">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <i class="bi bi-inbox display-1 text-muted"></i>
                            </div>
                            <h5 class="text-muted">No Open Deliverables Found</h5>
                            <p class="text-muted">There are currently no deliverables in "Open" status.</p>
                            <p class="small text-muted">
                                New deliverables are automatically created when:<br>
                                • <strong>Purchase Orders are created</strong> for closed feasibilities<br>
                                • Each deliverable includes complete PO details, costs, and client information<br>
                                • Only feasibilities with approved purchase orders will have deliverables
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if(session('success')): ?>
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050">
        <div class="toast show bg-success text-white" role="alert">
            <div class="toast-header bg-success text-white">
                <i class="bi bi-check-circle-fill me-2"></i>
                <strong class="me-auto">Success</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">
                <?php echo e(session('success')); ?>

            </div>
        </div>
    </div>
<?php endif; ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\wlcome\multipleuserpage\resources\views/operations/deliverables/open.blade.php ENDPATH**/ ?>