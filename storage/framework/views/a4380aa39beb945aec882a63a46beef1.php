

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow border-0">
                <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Edit Deliverable - <?php echo e($record->delivery_id); ?></h5>
                    <a href="<?php echo e(route('operations.deliverables.open')); ?>" class="btn btn-sm btn-outline-dark">
                        <i class="bi bi-arrow-left"></i> Back to Open
                    </a>
                </div>

                <div class="card-body">
                    <form action="<?php echo e(route('operations.deliverables.save', $record->id)); ?>" method="POST">
                        <?php echo csrf_field(); ?>

                        <!-- Site Information Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary border-bottom pb-2 mb-3">
                                    <i class="bi bi-geo-alt-fill me-2"></i>Site Information
                                </h6>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="site_address" class="form-label">Site Address</label>
                                <textarea class="form-control" id="site_address" name="site_address" rows="3" required><?php echo e(old('site_address', $record->site_address)); ?></textarea>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="local_contact" class="form-label">Local Contact</label>
                                <input type="text" class="form-control" id="local_contact" name="local_contact" 
                                       value="<?php echo e(old('local_contact', $record->local_contact)); ?>" required>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="state" class="form-label">State</label>
                                <input type="text" class="form-control" id="state" name="state" 
                                       value="<?php echo e(old('state', $record->state)); ?>" required>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="gst_number" class="form-label">GST Number</label>
                                <input type="text" class="form-control" id="gst_number" name="gst_number" 
                                       value="<?php echo e(old('gst_number', $record->gst_number)); ?>">
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="Open" <?php echo e($record->status == 'Open' ? 'selected' : ''); ?>>Open</option>
                                    <option value="InProgress" <?php echo e($record->status == 'InProgress' ? 'selected' : ''); ?>>In Progress</option>
                                    <option value="Delivery" <?php echo e($record->status == 'Delivery' ? 'selected' : ''); ?>>Delivery</option>
                                </select>
                            </div>
                        </div>

                        <!-- Network Configuration Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-success border-bottom pb-2 mb-3">
                                    <i class="bi bi-router-fill me-2"></i>Network Configuration
                                </h6>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="link_type" class="form-label">Link Type</label>
                                <select class="form-select" id="link_type" name="link_type" required>
                                    <option value="">Select Link Type</option>
                                    <option value="Leased Line" <?php echo e($record->link_type == 'Leased Line' ? 'selected' : ''); ?>>Leased Line</option>
                                    <option value="Broadband" <?php echo e($record->link_type == 'Broadband' ? 'selected' : ''); ?>>Broadband</option>
                                    <option value="MPLS" <?php echo e($record->link_type == 'MPLS' ? 'selected' : ''); ?>>MPLS</option>
                                </select>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="speed_in_mbps" class="form-label">Speed (Mbps)</label>
                                <input type="text" class="form-control" id="speed_in_mbps" name="speed_in_mbps" 
                                       value="<?php echo e(old('speed_in_mbps', $record->speed_in_mbps)); ?>" required>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="no_of_links" class="form-label">Number of Links</label>
                                <input type="number" class="form-control" id="no_of_links" name="no_of_links" 
                                       value="<?php echo e(old('no_of_links', $record->no_of_links)); ?>" min="1" max="4" required>
                            </div>
                        </div>

                        <!-- Vendor Information Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-info border-bottom pb-2 mb-3">
                                    <i class="bi bi-building me-2"></i>Vendor Information
                                </h6>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="vendor" class="form-label">Vendor</label>
                                <select class="form-select" id="vendor" name="vendor" required>
                                    <option value="">Select Vendor</option>
                                    <?php $__currentLoopData = $vendors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vendor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($vendor->vendor_name); ?>" 
                                                <?php echo e($record->vendor == $vendor->vendor_name ? 'selected' : ''); ?>>
                                            <?php echo e($vendor->vendor_name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="circuit_id" class="form-label">Circuit ID</label>
                                <input type="text" class="form-control" id="circuit_id" name="circuit_id" 
                                       value="<?php echo e(old('circuit_id', $record->circuit_id)); ?>">
                            </div>
                        </div>

                        <!-- Network Configuration Details -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-secondary border-bottom pb-2 mb-3">
                                    <i class="bi bi-gear-fill me-2"></i>Configuration Details
                                </h6>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="mode_of_delivery" class="form-label">Mode of Delivery</label>
                                <select class="form-select" id="mode_of_delivery" name="mode_of_delivery">
                                    <option value="">Select Mode</option>
                                    <option value="DHCP" <?php echo e($record->mode_of_delivery == 'DHCP' ? 'selected' : ''); ?>>DHCP</option>
                                    <option value="Static IP" <?php echo e($record->mode_of_delivery == 'Static IP' ? 'selected' : ''); ?>>Static IP</option>
                                    <option value="PPPoE" <?php echo e($record->mode_of_delivery == 'PPPoE' ? 'selected' : ''); ?>>PPPoE</option>
                                </select>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="static_ip_address" class="form-label">Static IP Address</label>
                                <input type="text" class="form-control" id="static_ip_address" name="static_ip_address" 
                                       value="<?php echo e(old('static_ip_address', $record->static_ip_address)); ?>">
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="date_of_activation" class="form-label">Date of Activation</label>
                                <input type="date" class="form-control" id="date_of_activation" name="date_of_activation" 
                                       value="<?php echo e(old('date_of_activation', $record->date_of_activation ? (is_string($record->date_of_activation) ? $record->date_of_activation : $record->date_of_activation->format('Y-m-d')) : '')); ?>">
                            </div>
                        </div>

                        <!-- PPPoE Configuration (Hidden by default) -->
                        <div class="row mb-4" id="pppoe_section" style="display: none;">
                            <div class="col-12">
                                <h6 class="text-warning border-bottom pb-2 mb-3">
                                    <i class="bi bi-key-fill me-2"></i>PPPoE Configuration
                                </h6>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="pppoe_username" class="form-label">PPPoE Username</label>
                                <input type="text" class="form-control" id="pppoe_username" name="pppoe_username" 
                                       value="<?php echo e(old('pppoe_username', $record->pppoe_username)); ?>">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="pppoe_password" class="form-label">PPPoE Password</label>
                                <input type="password" class="form-control" id="pppoe_password" name="pppoe_password" 
                                       value="<?php echo e(old('pppoe_password', $record->pppoe_password)); ?>">
                            </div>
                        </div>

                        <!-- Delivery Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-danger border-bottom pb-2 mb-3">
                                    <i class="bi bi-truck me-2"></i>Delivery Information
                                </h6>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="delivered_by" class="form-label">Delivered By</label>
                                <input type="text" class="form-control" id="delivered_by" name="delivered_by" 
                                       value="<?php echo e(old('delivered_by', $record->delivered_by)); ?>">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="delivery_notes" class="form-label">Delivery Notes</label>
                                <textarea class="form-control" id="delivery_notes" name="delivery_notes" rows="3"><?php echo e(old('delivery_notes', $record->delivery_notes)); ?></textarea>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="<?php echo e(route('operations.deliverables.open')); ?>" class="btn btn-secondary">
                                        <i class="bi bi-x-circle"></i> Cancel
                                    </a>
                                    
                                    <div>
                                        <button type="submit" name="action" value="save" class="btn btn-primary me-2">
                                            <i class="bi bi-check-circle"></i> Save Changes
                                        </button>
                                        
                                        <?php if($record->status != 'Delivery'): ?>
                                            <button type="submit" name="action" value="move_to_progress" class="btn btn-warning me-2">
                                                <i class="bi bi-arrow-right-circle"></i> Move to In Progress
                                            </button>
                                            
                                            <button type="submit" name="action" value="complete" class="btn btn-success">
                                                <i class="bi bi-check2-all"></i> Mark as Delivered
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
            </div>

            <!-- Feasibility Information Card (Read-only) -->
            <div class="card shadow border-0 mt-4">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Related Feasibility Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Feasibility ID:</strong><br>
                            <?php echo e($record->feasibility->feasibility_request_id ?? 'N/A'); ?>

                        </div>
                        <div class="col-md-3">
                            <strong>Client:</strong><br>
                            <?php echo e($record->feasibility->client->client_name ?? 'N/A'); ?>

                        </div>
                        <div class="col-md-3">
                            <strong>Created Date:</strong><br>
                            <?php echo e($record->feasibility->created_at->format('d M Y') ?? 'N/A'); ?>

                        </div>
                        <div class="col-md-3">
                            <strong>PO Number:</strong><br>
                            <?php echo e($record->po_number ?? 'N/A'); ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for Dynamic Form Sections -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modeSelect = document.getElementById('mode_of_delivery');
    const pppoeSection = document.getElementById('pppoe_section');
    
    function toggleSections() {
        if (modeSelect.value === 'PPPoE') {
            pppoeSection.style.display = 'block';
        } else {
            pppoeSection.style.display = 'none';
        }
    }
    
    modeSelect.addEventListener('change', toggleSections);
    toggleSections(); // Initialize on page load
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\wlcome\multipleuserpage\resources\views/operations/deliverables/edit.blade.php ENDPATH**/ ?>