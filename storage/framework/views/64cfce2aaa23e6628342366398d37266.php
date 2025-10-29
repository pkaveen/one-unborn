<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>One-Unborn</title>
    <link rel="icon" type="image/png" href="<?php echo e(asset('images/logo.jpg')); ?>">

    <!--  Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /*  Page background with gradient + centering layout */
        body {
            background: linear-gradient(to right, #1e73be, #155a96);
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        /*  White box styling for the form */
        .login-box {
            background: #fff;
            padding: 30px 25px;
            border-radius: 12px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0px 6px 15px rgba(0,0,0,0.25);
            text-align: center;
        }

        /*  Form title */
        .login-box h3 {
            margin-bottom: 20px;
            font-weight: bold;
            color: #1e73be;
        }

        /*  Input field styling */
        .form-control {
            border-radius: 8px;
            padding: 10px;
        }

        /*  Button style */
        .btn-primary {
            width: 100%;
            border-radius: 8px;
            padding: 10px;
            font-weight: bold;
            background-color: #1e73be;
            border: none;
        }

        /*  Button hover effect */
        .btn-primary:hover {
            background-color: #155a96;
        }

        /*  Alert message styling */
        .alert {
            text-align: left;
            font-size: 14px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <!--  Page Heading -->
        <h3>Forgot Password</h3>

        <!--  Success message if reset link sent -->
        <?php if(session('status')): ?>
            <div class="alert alert-success"><?php echo e(session('status')); ?></div>
        <?php endif; ?>

        <!--  Forgot Password Form -->
        <form method="POST" action="<?php echo e(route('password.email')); ?>">
            <?php echo csrf_field(); ?>  <!--  CSRF token for security -->

            <!--  Email Input -->
            <div class="mb-3 text-start">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="Enter your email" required autofocus>
                <!--  Validation error message -->
                <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> 
                    <span class="text-danger small"><?php echo e($message); ?></span> 
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <!--  Submit Button -->
            <button type="submit" class="btn btn-primary">Send Reset Link</button>
        </form>
    </div>
</body>
</html>
<?php /**PATH F:\xampp\htdocs\new\multipleuserpage\resources\views/auth/forgot-password.blade.php ENDPATH**/ ?>