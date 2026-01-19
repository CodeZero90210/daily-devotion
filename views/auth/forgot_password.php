<?php
$title = 'Forgot Password';
ob_start();
?>

<div class="auth-form">
    <h2>Forgot Password</h2>
    
    <?php if (isset($_SESSION['reset_url'])): ?>
        <div class="success" style="background-color: #d4edda; border: 1px solid #c3e6cb; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
            <h3 style="margin-top: 0; color: #155724;">Password Reset Link Generated</h3>
            <p><strong>Your password reset link has been generated. Since email is not available on this server, please use the link below:</strong></p>
            <p style="margin: 15px 0;">
                <a href="<?= htmlspecialchars($_SESSION['reset_url']) ?>" 
                   style="display: inline-block; padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; font-weight: bold;">
                    Click Here to Reset Password
                </a>
            </p>
            <p><strong>Or copy this link:</strong></p>
            <input type="text" 
                   value="<?= htmlspecialchars($_SESSION['reset_url']) ?>" 
                   readonly 
                   style="width: 100%; padding: 8px; font-size: 12px; border: 1px solid #ccc; border-radius: 3px; background-color: #f8f9fa;"
                   onclick="this.select(); document.execCommand('copy'); alert('Link copied to clipboard!');">
            <p style="font-size: 12px; color: #666; margin-top: 10px;">
                <strong>Note:</strong> This link will expire in 1 hour. If you need a new link, submit the form again.
            </p>
        </div>
        <?php 
        unset($_SESSION['reset_url']); 
        ?>
    <?php endif; ?>
    
    <p>Enter your email address to generate a password reset link.</p>
    
    <form method="POST" action="/forgot-password">
        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
        
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        
        <button type="submit">Send Reset Link</button>
    </form>
    
    <p><a href="/login">Back to Login</a></p>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
