

<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <h3 class="text-primary fw-bold mb-4">
        <i class="bi bi-envelope"></i> Email Configuration - <?php echo e($company->company_name); ?>

    </h3>

    <div class="card shadow border-0">
        <div class="card-body">
            <form action="<?php echo e(route('companies.save.email.config', $company->id)); ?>" method="POST">
                <?php echo csrf_field(); ?>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Mail Mailer</label>
                        <input type="text" name="mail_mailer" class="form-control" 
                               value="<?php echo e(old('mail_mailer', $setting->mail_mailer ?? 'smtp')); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Mail Host</label>
                        <input type="text" name="mail_host" class="form-control" 
                               value="<?php echo e(old('mail_host', $setting->mail_host ?? 'smtp.gmail.com')); ?>" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Mail Port</label>
                        <input type="number" name="mail_port" class="form-control" 
                               value="<?php echo e(old('mail_port', $setting->mail_port ?? 587)); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Mail Encryption</label>
                        <input type="text" name="mail_encryption" class="form-control" 
                               value="<?php echo e(old('mail_encryption', $setting->mail_encryption ?? 'tls')); ?>">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Mail Username (Email)</label>
                        <input type="email" name="mail_username" class="form-control" 
                               value="<?php echo e(old('mail_username', $setting->mail_username ?? '')); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Mail Password</label>
                        <input type="password" name="mail_password" class="form-control" 
                               value="<?php echo e(old('mail_password', $setting->mail_password ?? '')); ?>" required>
                    </div>
                </div>

                <hr>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">From Address</label>
                        <input type="email" name="mail_from_address" class="form-control" 
                               value="<?php echo e(old('mail_from_address', $setting->mail_from_address ?? '')); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">From Name</label>
                        <input type="text" name="mail_from_name" class="form-control" 
                               value="<?php echo e(old('mail_from_name', $setting->mail_from_name ?? $company->company_name)); ?>" required>
                    </div>
                </div>

                <div class="text-end">
                    <a href="<?php echo e(route('companies.index')); ?>" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Save Configuration
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views/companies/email_config.blade.php ENDPATH**/ ?>