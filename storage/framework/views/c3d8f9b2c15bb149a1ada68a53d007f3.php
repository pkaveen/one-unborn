

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <h4 class="text-primary fw-bold mb-3">Edit Feasibility</h4>

    <div class="card shadow border-0 p-4">

        <?php if($errors->any()): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?php echo e(route('feasibility.update', $feasibility->id)); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div class="row g-3">

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Feasibility Request ID</label>
                    <input type="text" class="form-control bg-light" value="<?php echo e($feasibility->feasibility_request_id); ?>" readonly>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Type of Service <span class="text-danger">*</span></label>
                    <select name="type_of_service" id="type_of_service" class="form-select" required>
                        <option value="">Select</option>
                        <option value="Broadband" <?php echo e($feasibility->type_of_service=='Broadband'?'selected':''); ?>>Broadband</option>
                        <option value="ILL" <?php echo e($feasibility->type_of_service=='ILL'?'selected':''); ?>>ILL</option>
                        <option value="P2P" <?php echo e($feasibility->type_of_service=='P2P'?'selected':''); ?>>P2P</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Company <span class="text-danger">*</span></label>
                    <select name="company_id" id="company_id" class="form-select" required>
                        <option value="">Select Company</option>
                        <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $company): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($company->id); ?>" <?php echo e($feasibility->company_id==$company->id?'selected':''); ?>>
                                <?php echo e($company->company_name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Client Name <span class="text-danger">*</span></label>
                    <select name="client_id" id="client_id" class="form-select" required>
                        <option value="">Select Client</option>
                        <?php $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($client->id); ?>" <?php echo e($feasibility->client_id==$client->id?'selected':''); ?>>
                                <?php echo e($client->business_name ?: $client->client_name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Pincode <span class="text-danger">*</span></label>
                    <input type="text" name="pincode" id="pincode" maxlength="6" value="<?php echo e($feasibility->pincode); ?>" class="form-control" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">State <span class="text-danger">*</span></label>
                    <select name="state" id="state" class="form-select select2-tags">
                        <option value="">Select or Type State</option>
                        <option value="<?php echo e($feasibility->state); ?>" selected><?php echo e($feasibility->state); ?></option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">District <span class="text-danger">*</span></label>
                    <select name="district" id="district" class="form-select select2-tags">
                        <option value="">Select or Type District</option>
                        <option value="<?php echo e($feasibility->district); ?>" selected><?php echo e($feasibility->district); ?></option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Area <span class="text-danger">*</span></label>
                    <select name="area" id="post_office" class="form-select select2-tags">
                        <option value="">Select or Type Area</option>
                        <option value="<?php echo e($feasibility->area); ?>" selected><?php echo e($feasibility->area); ?></option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Address <span class="text-danger">*</span></label>
                    <textarea name="address" class="form-control" rows="2" required><?php echo e($feasibility->address); ?></textarea>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">SPOC Name <span class="text-danger">*</span></label>
                    <input type="text" name="spoc_name" value="<?php echo e($feasibility->spoc_name); ?>" class="form-control" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">SPOC Contact 1 <span class="text-danger">*</span></label>
                    <input type="text" name="spoc_contact1" value="<?php echo e($feasibility->spoc_contact1); ?>" class="form-control" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">SPOC Contact 2</label>
                    <input type="text" name="spoc_contact2" value="<?php echo e($feasibility->spoc_contact2); ?>" class="form-control">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">SPOC Email</label>
                    <input type="email" name="spoc_email" value="<?php echo e($feasibility->spoc_email); ?>" class="form-control">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">No. of Links <span class="text-danger">*</span></label>
                    <select name="no_of_links" id="no_of_links" class="form-select" required>
                        <option value="">Select</option>
                        <option <?php echo e($feasibility->no_of_links==1?'selected':''); ?>>1</option>
                        <option <?php echo e($feasibility->no_of_links==2?'selected':''); ?>>2</option>
                        <option <?php echo e($feasibility->no_of_links==3?'selected':''); ?>>3</option>
                        <option <?php echo e($feasibility->no_of_links==4?'selected':''); ?>>4</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Vendor Type <span class="text-danger">*</span></label>
                    <select name="vendor_type" id="vendor_type" class="form-select" required>
                        <option value="">Select</option>
                        <option <?php echo e($feasibility->vendor_type=='Same Vendor'?'selected':''); ?>>Same Vendor</option>
                        <option <?php echo e($feasibility->vendor_type=='Different Vendor'?'selected':''); ?>>Different Vendor</option>
                        <option <?php echo e($feasibility->vendor_type=='UBN'?'selected':''); ?>>UBN</option>
                        <option <?php echo e($feasibility->vendor_type=='UBS'?'selected':''); ?>>UBS</option>
                        <option <?php echo e($feasibility->vendor_type=='UBL'?'selected':''); ?>>UBL</option>
                        <option <?php echo e($feasibility->vendor_type=='INF'?'selected':''); ?>>INF</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Speed <span class="text-danger">*</span></label>
                    <input type="text" name="speed" value="<?php echo e($feasibility->speed); ?>" class="form-control" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Static IP <span class="text-danger">*</span></label>
                    <select name="static_ip" id="static_ip" class="form-select" required>
                        <option value="">Select</option>
                        <option value="Yes" <?php echo e($feasibility->static_ip=='Yes'?'selected':''); ?>>Yes</option>
                        <option value="No" <?php echo e($feasibility->static_ip=='No'?'selected':''); ?>>No</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Static IP Subnet</label>
                    <select name="static_ip_subnet" id="static_ip_subnet" class="form-select" <?php echo e($feasibility->static_ip=='Yes'?'':'disabled'); ?>>
                        <option value="">Select Subnet</option>
                        <?php $__currentLoopData = ['/32','/31','/30','/29','/28','/27','/26','/25','/24']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($sub); ?>" <?php echo e($feasibility->static_ip_subnet==$sub?'selected':''); ?>><?php echo e($sub); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Expected Delivery <span class="text-danger">*</span></label>
                    <input type="date" name="expected_delivery" value="<?php echo e($feasibility->expected_delivery); ?>" class="form-control" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Expected Activation <span class="text-danger">*</span></label>
                    <input type="date" name="expected_activation" value="<?php echo e($feasibility->expected_activation); ?>" class="form-control" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Hardware Required <span class="text-danger">*</span></label>
                    <select name="hardware_required" id="hardware_required" class="form-select" required>
                        <option value="">Select</option>
                        <option value="1" <?php echo e($feasibility->hardware_required==1?'selected':''); ?>>Yes</option>
                        <option value="0" <?php echo e($feasibility->hardware_required==0?'selected':''); ?>>No</option>
                    </select>
                </div>

                <div class="col-md-3" id="hardware_name_div" >
                    <label class="form-label fw-semibold">Hardware Model Name</label>
                    <input type="text" name="hardware_model_name" value="<?php echo e($feasibility->hardware_model_name); ?>" class="form-control">
                </div>

                <input type="hidden" name="status" value="<?php echo e($feasibility->status); ?>">
            </div>

            <div class="mt-4 text-end">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Update</button>
                <a href="<?php echo e(route('feasibility.index')); ?>" class="btn btn-secondary">Cancel</a>
            </div>

        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\wlcome\multipleuserpage\resources\views/feasibility/edit.blade.php ENDPATH**/ ?>