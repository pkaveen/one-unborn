

<?php $__env->startSection('content'); ?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">

           
        <?php if($errors->any()): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>@endif

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Create Your Profile</h5>
                </div>

                <div class="card-body">
                    <!-- IMPORTANT: enctype for file upload -->
                    <form method="POST" action="<?php echo e(route('profile.store')); ?>" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>

                        
                        <div class="mb-3">
                            <label class="form-label">First Name</label>
                            <input type="text" class="form-control" name="fname" value="<?php echo e(old('fname')); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Last Name</label>
                            <input type="text" class="form-control" name="lname" value="<?php echo e(old('lname')); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Designation</label>
                            <input type="text" class="form-control" name="designation" value="<?php echo e(old('designation')); ?>" required>
                        </div>

                        
                        <h5 class="text-secondary mt-3">Address</h5>
                        <input type="text" name="address1" class="form-control mb-2" placeholder="Address Line 1">
                        <input type="text" name="address2" class="form-control mb-2" placeholder="Address Line 2">
                        <input type="text" name="address3" class="form-control mb-2" placeholder="Address Line 3">

                        
                        <div class="mb-3">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" name="Date_of_Birth" class="form-control" placeholder="select DOB" value="<?php echo e(old('Date_of_Birth')); ?>" required>

                        </div>

                        
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="<?php echo e(old('email')); ?>" required>
                        </div>

                        
                        <div class="mb-3">
                            <label class="form-label">Phone Number 1</label>
                            <input type="number" class="form-control" name="phone1" value="<?php echo e(old('phone1')); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Phone Number 2</label>
                            <input type="number" class="form-control" name="phone2" value="<?php echo e(old('phone2')); ?>">
                        </div>

                        
                        <div class="mb-3">
                            <label class="form-label">Aadhaar Number</label>
                            <input type="number" class="form-control" name="aadhaar_number" value="<?php echo e(old('aadhaar_number')); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Aadhaar Upload</label>
                            <input type="file" class="form-control" name="aadhaar_upload" required>
                        </div>

                        
                        <div class="mb-3">
                            <label class="form-label">PAN Number</label>
                            <input type="text" name="pan" class="form-control mb-2" value="<?php echo e(old('pan')); ?>" placeholder="PAN No">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">PAN Upload</label>
                            <input type="file" class="form-control" name="pan_upload" required>
                        </div>

                        
                        <h5 class="text-secondary mt-3">Bank Details</h5>
                        <input type="text" name="bank_name" class="form-control mb-2" placeholder="Bank Name">
                        <input type="text" name="branch" class="form-control mb-2" placeholder="Branch">
                        <input type="text" name="bank_account_no" class="form-control mb-2" placeholder="Account No">
                        <input type="text" name="ifsc_code" class="form-control mb-3" placeholder="IFSC Code">

                        <button type="submit" class="btn btn-success w-100">Save Profile</button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views/profile/create.blade.php ENDPATH**/ ?>