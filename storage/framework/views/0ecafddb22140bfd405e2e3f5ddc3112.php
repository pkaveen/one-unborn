

<?php $__env->startSection('content'); ?>
<div class="container mt-5">
    <div class="card shadow-sm p-4">
        <h2 class="mb-4">My Profile</h2>

        
        <?php if(session('success')): ?>
            <div class="alert alert-success"><?php echo e(session('success')); ?></div>
        <?php endif; ?>

        <?php if(session('alert')): ?>
            <div class="alert alert-warning"><?php echo e(session('alert')); ?></div>
        <?php endif; ?>

        
        <?php if(isset($profile)): ?>
        <table class="table table-bordered">
            <tr><th>First Name</th><td><?php echo e($profile->fname ?? 'N/A'); ?></td></tr>
            <tr><th>Last Name</th><td><?php echo e($profile->lname ?? 'N/A'); ?></td></tr>
            <tr><th>Designation</th><td><?php echo e($profile->designation ?? 'N/A'); ?></td></tr>

            <tr><th>Email</th><td><?php echo e($profile->email ?? 'N/A'); ?></td></tr>
            <tr><th>Phone 1</th><td><?php echo e($profile->phone1 ?? 'N/A'); ?></td></tr>
            <tr><th>Phone 2</th><td><?php echo e($profile->phone2 ?? '-'); ?></td></tr>
            <tr><th>Date of Birth</th><td><?php echo e($profile->Date_of_Birth ?? 'N/A'); ?></td></tr>

            
            <tr>
                <th>Address</th>
                <td>
                    <?php echo e($profile->address1 ?? ''); ?><br>
                    <?php echo e($profile->address2 ?? ''); ?><br>
                    <?php echo e($profile->address3 ?? ''); ?>

                </td>
            </tr>

            
            <tr><th>Aadhaar Number</th><td><?php echo e($profile->aadhaar_number ?? 'N/A'); ?></td></tr>
            <tr>
                <th>Aadhaar Upload</th>
                <td>
                    <?php if(!empty($profile->aadhaar_upload)): ?>
                        <a href="<?php echo e(asset($profile->aadhaar_upload)); ?>" target="_blank">View Aadhaar</a>
                    <?php else: ?>
                        Not Uploaded
                    <?php endif; ?>
                </td>
            </tr>

            
            <tr><th>PAN Number</th><td><?php echo e($profile->pan ?? 'N/A'); ?></td></tr>
            <tr>
                <th>PAN Upload</th>
                <td>
                    <?php if(!empty($profile->pan_upload)): ?>
                        <a href="<?php echo e(asset($profile->pan_upload)); ?>" target="_blank">View PAN</a>
                    <?php else: ?>
                        Not Uploaded
                    <?php endif; ?>
                </td>
            </tr>

            
            <tr><th>Bank Name</th><td><?php echo e($profile->bank_name ?? 'N/A'); ?></td></tr>
            <tr><th>Branch</th><td><?php echo e($profile->branch ?? 'N/A'); ?></td></tr>
            <tr><th>Account Number</th><td><?php echo e($profile->bank_account_no ?? 'N/A'); ?></td></tr>
            <tr><th>IFSC Code</th><td><?php echo e($profile->ifsc_code ?? 'N/A'); ?></td></tr>
        </table>

        <?php else: ?>
        <div class="alert alert-info">
            No profile found. Please create your profile below.
        </div>
        <?php endif; ?>

        <div class="mt-4 d-flex justify-content-between">
            <a href="<?php echo e(route('profile.create')); ?>" class="btn btn-primary">
                Edit / Create Profile
            </a>

            <a href="<?php echo e(route('welcome')); ?>" class="btn btn-secondary">
                Back to Dashboard
            </a>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\new\multipleuserpage\resources\views/profile/index.blade.php ENDPATH**/ ?>