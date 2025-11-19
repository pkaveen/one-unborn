<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feasibility Status Update</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #007bff; color: white; padding: 15px; border-radius: 5px 5px 0 0; }
        .content { background-color: #f8f9fa; padding: 20px; border-radius: 0 0 5px 5px; }
        .status-badge { padding: 5px 10px; border-radius: 3px; color: white; font-weight: bold; }
        .status-open { background-color: #17a2b8; }
        .status-inprogress { background-color: #ffc107; color: #000; }
        .status-closed { background-color: #28a745; }
        .info-table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        .info-table td { padding: 8px; border-bottom: 1px solid #ddd; }
        .info-table .label { font-weight: bold; width: 40%; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2 style="margin: 0;">
                <?php if($status == 'InProgress'): ?>
                    🔄 Feasibility Status: In Progress
                <?php elseif($status == 'Closed'): ?>
                    ✅ Feasibility Request Completed
                <?php else: ?>
                    📋 Feasibility Status Updated
                <?php endif; ?>
            </h2>
        </div>
        
        <div class="content">
            <?php if($status == 'InProgress'): ?>
                <p><strong>Good news!</strong> Your feasibility request is now being processed by our Operations team.</p>
            <?php elseif($status == 'Closed'): ?>
                <?php if($previousStatus == 'Open'): ?>
                    <p><strong>Excellent news!</strong> Your feasibility request has been completed directly with vendor quotations.</p>
                    <p><em>Our team efficiently processed your request from initial review to completion.</em></p>
                <?php else: ?>
                    <p><strong>Great news!</strong> Your feasibility request has been completed with vendor quotations.</p>
                <?php endif; ?>
            <?php else: ?>
                <p>Your feasibility request status has been updated.</p>
            <?php endif; ?>

            <table class="info-table">
                <tr>
                    <td class="label">Request ID:</td>
                    <td><strong><?php echo e($feasibility->feasibility_request_id ?? 'N/A'); ?></strong></td>
                </tr>
                <tr>
                    <td class="label">Client:</td>
                    <td><?php echo e($feasibility->client->client_name ?? 'N/A'); ?></td>
                </tr>
                <tr>
                    <td class="label">Service Type:</td>
                    <td><?php echo e($feasibility->type_of_service ?? 'N/A'); ?></td>
                </tr>
                <tr>
                    <td class="label">Location:</td>
                    <td><?php echo e($feasibility->area ?? 'N/A'); ?>, <?php echo e($feasibility->district ?? 'N/A'); ?> - <?php echo e($feasibility->pincode ?? 'N/A'); ?></td>
                </tr>
                <tr>
                    <td class="label">No. of Links:</td>
                    <td><?php echo e($feasibility->no_of_links ?? 'N/A'); ?></td>
                </tr>
                <?php if($previousStatus): ?>
                <tr>
                    <td class="label">Previous Status:</td>
                    <td><span class="status-badge status-<?php echo e(strtolower($previousStatus)); ?>"><?php echo e($previousStatus); ?></span></td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td class="label">Current Status:</td>
                    <td><span class="status-badge status-<?php echo e(strtolower($status)); ?>"><?php echo e($status); ?></span></td>
                </tr>
                <?php if($actionBy): ?>
                <tr>
                    <td class="label">Updated By:</td>
                    <td><?php echo e($actionBy->name ?? 'System'); ?> (<?php echo e($actionBy->email ?? 'N/A'); ?>)</td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td class="label">Updated At:</td>
                    <td><?php echo e(now()->format('d-M-Y h:i A')); ?></td>
                </tr>
            </table>

            <?php if($status == 'InProgress'): ?>
                <div style="background-color: #fff3cd; padding: 15px; border-radius: 5px; border-left: 4px solid #ffc107; margin: 15px 0;">
                    <strong>Next Steps:</strong>
                    <ul style="margin: 10px 0;">
                        <li>Operations team is collecting vendor quotations</li>
                        <li>You will be notified once the analysis is complete</li>
                        <li>Expected timeline: 2-3 business days</li>
                    </ul>
                </div>
            <?php elseif($status == 'Closed'): ?>
                <div style="background-color: #d1edff; padding: 15px; border-radius: 5px; border-left: 4px solid #007bff; margin: 15px 0;">
                    <strong>What's Next:</strong>
                    <ul style="margin: 10px 0;">
                        <li>Vendor quotations and delivery timelines are now available</li>
                        <li>Please login to your dashboard to view detailed analysis</li>
                        <li>Contact your account manager for next steps</li>
                    </ul>
                </div>
            <?php endif; ?>

            <hr style="margin: 20px 0; border: none; border-top: 1px solid #ddd;">
            
            <p style="color: #666; font-size: 14px;">
                <strong>Need Help?</strong><br>
                If you have any questions about this feasibility request, please contact your account manager or reply to this email.
            </p>

            <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;">
                <p style="margin: 0; color: #666;">
                    Best regards,<br>
                    <strong><?php echo e(config('app.name', 'Network Solutions Team')); ?></strong>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
<?php /**PATH F:\xampp\htdocs\wlcome\multipleuserpage\resources\views/emails/feasibility/status.blade.php ENDPATH**/ ?>