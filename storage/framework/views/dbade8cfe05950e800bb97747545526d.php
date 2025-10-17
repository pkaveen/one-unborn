

<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <h3 class="mb-3 text-primary">View Company</h3>

    <div class="card shadow border-0 p-4">
        <table class="table table-bordered">
            <tr>
                <th>Company Name</th>
                <td><?php echo e($company->company_name); ?></td>
            </tr>
            <tr>
                <th>CIN / LLPIN</th>
                <td><?php echo e($company->cin_llpin ?? '-'); ?></td>
            </tr>
            <tr>
                <th>Contact No</th>
                <td><?php echo e($company->contact_no ?? '-'); ?></td>
            </tr>
            <tr>
                <th>Phone No</th>
                <td><?php echo e($company->phone_no ?? '-'); ?></td>
            </tr>
            <tr>
                <th>Email 1</th>
                <td><?php echo e($company->email_1 ?? '-'); ?></td>
            </tr>
            <tr>
                <th>Email 2</th>
                <td><?php echo e($company->email_2 ?? '-'); ?></td>
            </tr>
            <tr>
                <th>Address</th>
                <td><?php echo e($company->address ?? '-'); ?></td>
            </tr>

            <tr>
                <th>Billing Logo</th>
                <td>
                    <?php if(!empty($company->billing_logo)): ?>
                        <img src="<?php echo e(asset('storage/'.$company->billing_logo)); ?>" width="120" class="border rounded">
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
            </tr>

            <tr>
                <th>Normal Sign</th>
                <td>
                    <?php if(!empty($company->billing_sign_normal)): ?>
                        <img src="<?php echo e(asset('storage/'.$company->billing_sign_normal)); ?>" width="120" class="border rounded">
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
            </tr>

            <tr>
                <th>Digital Sign</th>
                <td>
                    <?php if(!empty($company->billing_sign_digital)): ?>
                        <img src="<?php echo e(asset('storage/'.$company->billing_sign_digital)); ?>" width="120" class="border rounded">
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
            </tr>

            <tr>
                <th>GST No</th>
                <td><?php echo e($company->gst_no ?? '-'); ?></td>
            </tr>
            <tr>
                <th>PAN Number</th>
                <td><?php echo e($company->pan_number ?? '-'); ?></td>
            </tr>
            <tr>
                <th>TAN Number</th>
                <td><?php echo e($company->tan_number ?? '-'); ?></td>
            </tr>
            <tr>
                <th>Status</th>
                <td>
                    <span class="badge <?php echo e($company->status === 'Active' ? 'bg-success' : 'bg-danger'); ?>">
                        <?php echo e($company->status); ?>

                    </span>
                </td>
            </tr>
        </table>

        <div class="text-end">
            <a href="<?php echo e(route('companies.index')); ?>" class="btn btn-secondary">Back</a>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views/companies/view.blade.php ENDPATH**/ ?>