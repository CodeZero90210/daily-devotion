<?php
$title = 'Reset Password';
ob_start();
?>

<div class="auth-form">
    <h2>Reset Password</h2>
    
    <form method="POST" action="/reset-password">
        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
        
        <div class="form-group">
            <label for="password">New Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
        
        <div class="form-group">
            <label for="password_confirm">Confirm New Password:</label>
            <input type="password" id="password_confirm" name="password_confirm" required>
        </div>
        
        <button type="submit">Reset Password</button>
    </form>
    
    <p><a href="/login">Back to Login</a></p>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
