

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow border-0">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-eye me-2"></i>Deliverable Details - <?php echo e($record->delivery_id); ?>

                    </h5>
                    <div class="d-flex gap-2">
                        <span class="badge bg-light text-dark"><?php echo e($record->status); ?></span>
                        <a href="<?php echo e(route('operations.deliverables.edit', $record->id)); ?>" class="btn btn-light btn-sm">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <a href="<?php echo e(route('operations.deliverables.open')); ?>" class="btn btn-outline-light btn-sm">
                            <i class="bi bi-arrow-left"></i> Back
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <!-- Left Column - Basic Information -->
                        <div class="col-lg-6">
                            <h6 class="text-primary mb-3"><i class="bi bi-info-circle"></i> Basic Information</h6>
                            
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Delivery ID:</th>
                                    <td><strong class="text-primary"><?php echo e($record->delivery_id); ?></strong></td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        <span class="badge 
                                            <?php if($record->status == 'Open'): ?> bg-warning text-dark
                                            <?php elseif($record->status == 'InProgress'): ?> bg-primary
                                            <?php elseif($record->status == 'Delivery'): ?> bg-success
                                            <?php else: ?> bg-secondary <?php endif; ?>">
                                            <?php echo e($record->status); ?>

                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Created Date:</th>
                                    <td><?php echo e($record->created_at->format('d-M-Y H:i')); ?></td>
                                </tr>
                            </table>

                            <h6 class="text-primary mb-3 mt-4"><i class="bi bi-building"></i> Client & Site Information</h6>
                            
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Client:</th>
                                    <td>
                                        <?php if($record->feasibility && $record->feasibility->client): ?>
                                            <strong><?php echo e($record->feasibility->client->client_name); ?></strong>
                                        <?php else: ?>
                                            <span class="text-muted">No Client</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Feasibility ID:</th>
                                    <td>
                                        <?php if($record->feasibility): ?>
                                            <span class="badge bg-info"><?php echo e($record->feasibility->feasibility_request_id); ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Site Address:</th>
                                    <td><?php echo e($record->site_address ?? 'Not specified'); ?></td>
                                </tr>
                                <tr>
                                    <th>Local Contact:</th>
                                    <td><?php echo e($record->local_contact ?? 'Not specified'); ?></td>
                                </tr>
                                <tr>
                                    <th>State:</th>
                                    <td><?php echo e($record->state ?? 'Not specified'); ?></td>
                                </tr>
                                <tr>
                                    <th>GST Number:</th>
                                    <td><?php echo e($record->gst_number ?? 'Not specified'); ?></td>
                                </tr>
                            </table>
                        </div>

                        <!-- Right Column - Purchase Order Information -->
                        <div class="col-lg-6">
                            <?php if($record->purchaseOrder): ?>
                            <h6 class="text-success mb-3"><i class="bi bi-receipt"></i> Purchase Order Details</h6>
                            
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">PO Number:</th>
                                    <td><strong class="text-success"><?php echo e($record->purchaseOrder->po_number); ?></strong></td>
                                </tr>
                                <tr>
                                    <th>PO Date:</th>
                                    <td><?php echo e($record->purchaseOrder->po_date->format('d-M-Y')); ?></td>
                                </tr>
                                <tr>
                                    <th>Contract Period:</th>
                                    <td><?php echo e($record->purchaseOrder->contract_period); ?> Months</td>
                                </tr>
                                <tr>
                                    <th>Number of Links:</th>
                                    <td><span class="badge bg-primary"><?php echo e($record->purchaseOrder->no_of_links); ?> Links</span></td>
                                </tr>
                            </table>

                            <h6 class="text-success mb-3 mt-4"><i class="bi bi-calculator"></i> Cost Breakdown</h6>
                            
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">ARC per Link:</th>
                                    <td class="text-end">₹<?php echo e(number_format($record->purchaseOrder->arc_per_link, 2)); ?></td>
                                </tr>
                                <tr>
                                    <th>OTC per Link:</th>
                                    <td class="text-end">₹<?php echo e(number_format($record->purchaseOrder->otc_per_link, 2)); ?></td>
                                </tr>
                                <tr>
                                    <th>Static IP per Link:</th>
                                    <td class="text-end">₹<?php echo e(number_format($record->purchaseOrder->static_ip_cost_per_link, 2)); ?></td>
                                </tr>
                                <tr class="table-success">
                                    <th>Total per Link:</th>
                                    <td class="text-end">
                                        <strong>₹<?php echo e(number_format($record->purchaseOrder->arc_per_link + $record->purchaseOrder->otc_per_link + $record->purchaseOrder->static_ip_cost_per_link, 2)); ?></strong>
                                    </td>
                                </tr>
                                <tr class="table-primary">
                                    <th>Grand Total (<?php echo e($record->purchaseOrder->no_of_links); ?> Links):</th>
                                    <td class="text-end">
                                        <?php
                                            $grandTotal = ($record->purchaseOrder->arc_per_link + $record->purchaseOrder->otc_per_link + $record->purchaseOrder->static_ip_cost_per_link) * $record->purchaseOrder->no_of_links;
                                        ?>
                                        <strong class="text-primary fs-5">₹<?php echo e(number_format($grandTotal, 2)); ?></strong>
                                    </td>
                                </tr>
                            </table>
                            <?php else: ?>
                            <h6 class="text-muted mb-3"><i class="bi bi-exclamation-triangle"></i> Purchase Order Information</h6>
                            <div class="alert alert-warning">
                                <i class="bi bi-info-circle"></i> No purchase order linked to this deliverable.
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Technical Information -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3"><i class="bi bi-gear"></i> Technical Specifications</h6>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="40%">Link Type:</th>
                                            <td><?php echo e($record->link_type ?? 'Not specified'); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Speed (Mbps):</th>
                                            <td><?php echo e($record->speed_in_mbps ?? 'Not specified'); ?> Mbps</td>
                                        </tr>
                                        <tr>
                                            <th>Vendor:</th>
                                            <td><?php echo e($record->vendor ?? 'Not assigned'); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Circuit ID:</th>
                                            <td><?php echo e($record->circuit_id ?? 'Not assigned'); ?></td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="40%">Plans Name:</th>
                                            <td><?php echo e($record->plans_name ?? 'Not specified'); ?></td>
                                        </tr>
                                        <tr>
                                            <th>SLA:</th>
                                            <td><?php echo e($record->sla ?? 'Not specified'); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Mode of Delivery:</th>
                                            <td><?php echo e($record->mode_of_delivery ?? 'Not specified'); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Status of Link:</th>
                                            <td><?php echo e($record->status_of_link ?? 'Not specified'); ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if($record->purchaseOrder): ?>
                    <!-- Individual Link Pricing (if available) -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h6 class="text-success mb-3"><i class="bi bi-list-ol"></i> Individual Link Pricing Details</h6>
                            
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Link #</th>
                                            <th>ARC Amount</th>
                                            <th>OTC Amount</th>
                                            <th>Static IP Cost</th>
                                            <th>Total per Link</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php for($i = 1; $i <= $record->purchaseOrder->no_of_links; $i++): ?>
                                            <?php
                                                $arcLink = $record->purchaseOrder->{"arc_link_{$i}"} ?? $record->purchaseOrder->arc_per_link;
                                                $otcLink = $record->purchaseOrder->{"otc_link_{$i}"} ?? $record->purchaseOrder->otc_per_link;
                                                $staticLink = $record->purchaseOrder->{"static_ip_link_{$i}"} ?? $record->purchaseOrder->static_ip_cost_per_link;
                                                $linkTotal = $arcLink + $otcLink + $staticLink;
                                            ?>
                                            <tr>
                                                <td><strong>Link <?php echo e($i); ?></strong></td>
                                                <td>₹<?php echo e(number_format($arcLink, 2)); ?></td>
                                                <td>₹<?php echo e(number_format($otcLink, 2)); ?></td>
                                                <td>₹<?php echo e(number_format($staticLink, 2)); ?></td>
                                                <td><strong>₹<?php echo e(number_format($linkTotal, 2)); ?></strong></td>
                                            </tr>
                                        <?php endfor; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Action Buttons -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex gap-2 justify-content-end">
                                <a href="<?php echo e(route('operations.deliverables.edit', $record->id)); ?>" class="btn btn-primary">
                                    <i class="bi bi-pencil"></i> Edit Deliverable
                                </a>
                                <?php if($record->purchaseOrder): ?>
                                <a href="<?php echo e(route('sm.purchaseorder.view', $record->purchaseOrder->id)); ?>" class="btn btn-success" target="_blank">
                                    <i class="bi bi-eye"></i> View Purchase Order
                                </a>
                                <?php endif; ?>
                                <a href="<?php echo e(route('operations.deliverables.open')); ?>" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left"></i> Back to List
                                </a>
                            </div>
                        </div>
                    </div>
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
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\wlcome\multipleuserpage\resources\views/operations/deliverables/view.blade.php ENDPATH**/ ?>