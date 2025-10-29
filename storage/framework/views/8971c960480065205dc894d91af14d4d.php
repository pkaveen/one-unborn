

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold text-primary">Feasibility Status - <?php echo e(ucfirst($status)); ?></h4>
    </div>

    
    <ul class="nav nav-tabs mb-3">
        <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tab): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li class="nav-item">
                <a class="nav-link <?php echo e($tab == $status ? 'active' : ''); ?>"
                   href="<?php echo e(route('feasibility.status.index', $tab)); ?>">
                   <?php echo e($tab); ?>

                </a>
            </li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>

    
    <div class="card shadow-sm border-0">
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-primary text-center">
                    <tr>
                        <th rowspan="2">S.No</th>
                        <th rowspan="2">Client</th>
                        <th colspan="5">Vendor 1</th>
                        <th colspan="5">Vendor 2</th>
                        <th colspan="5">Vendor 3</th>
                        <th colspan="5">Vendor 4</th>
                        <th rowspan="2">Status</th>
                        <th rowspan="2">Action</th>
                    </tr>
                    <tr>
                        <th>Name</th>
                        <th>ARC</th>
                        <th>OTC</th>
                        <th>Static IP Cost</th>
                        <th>Delivery Timeline</th>

                        <th>Name</th>
                        <th>ARC</th>
                        <th>OTC</th>
                        <th>Static IP Cost</th>
                        <th>Delivery Timeline</th>

                        <th>Name</th>
                        <th>ARC</th>
                        <th>OTC</th>
                        <th>Static IP Cost</th>
                        <th>Delivery Timeline</th>

                        <th>Name</th>
                        <th>ARC</th>
                        <th>OTC</th>
                        <th>Static IP Cost</th>
                        <th>Delivery Timeline</th>
                    </tr>
                </thead>

                <tbody class="text-center">
                    <?php $__empty_1 = true; $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($key + 1); ?></td>
                            <td><?php echo e($record->feasibility->client->client_name ?? 'N/A'); ?></td>

                            
                            <td><?php echo e($record->vendor1_name ?? '-'); ?></td>
                            <td><?php echo e($record->vendor1_arc ?? '-'); ?></td>
                            <td><?php echo e($record->vendor1_otc ?? '-'); ?></td>
                            <td><?php echo e($record->vendor1_static_ip_cost ?? '-'); ?></td>
                            <td><?php echo e($record->vendor1_delivery_timeline ?? '-'); ?></td>

                            
                            <td><?php echo e($record->vendor2_name ?? '-'); ?></td>
                            <td><?php echo e($record->vendor2_arc ?? '-'); ?></td>
                            <td><?php echo e($record->vendor2_otc ?? '-'); ?></td>
                            <td><?php echo e($record->vendor2_static_ip_cost ?? '-'); ?></td>
                            <td><?php echo e($record->vendor2_delivery_timeline ?? '-'); ?></td>

                            
                            <td><?php echo e($record->vendor3_name ?? '-'); ?></td>
                            <td><?php echo e($record->vendor3_arc ?? '-'); ?></td>
                            <td><?php echo e($record->vendor3_otc ?? '-'); ?></td>
                            <td><?php echo e($record->vendor3_static_ip_cost ?? '-'); ?></td>
                            <td><?php echo e($record->vendor3_delivery_timeline ?? '-'); ?></td>

                            
                            <td><?php echo e($record->vendor4_name ?? '-'); ?></td>
                            <td><?php echo e($record->vendor4_arc ?? '-'); ?></td>
                            <td><?php echo e($record->vendor4_otc ?? '-'); ?></td>
                            <td><?php echo e($record->vendor4_static_ip_cost ?? '-'); ?></td>
                            <td><?php echo e($record->vendor4_delivery_timeline ?? '-'); ?></td>

                            
                            <td>
                                <span class="badge bg-<?php echo e($record->status == 'Closed' ? 'success' : ($record->status == 'InProgress' ? 'warning' : 'secondary')); ?>">
                                    <?php echo e($record->status); ?>

                                </span>
                            </td>

                            
                            <td>
                                <a href="<?php echo e(route('feasibility.status.show', $record->id)); ?>" class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i> 
                                </a>
                                <a href="<?php echo e(route('feasibility.status.edit', $record->id)); ?>" class="btn btn-sm btn-primary">
                                    <i class="bi bi-pencil-square"></i> 
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="28" class="text-center text-muted">No records found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\new\multipleuserpage\resources\views/feasibility/feasibility_status/index.blade.php ENDPATH**/ ?>