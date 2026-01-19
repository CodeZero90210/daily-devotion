<?php
$title = 'Login';
ob_start();
?>

<div class="auth-form">
    <h2>Login</h2>
    <form method="POST" action="/login">
        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
        
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
        
        <button type="submit">Login</button>
    </form>
    
    <p><a href="/forgot-password">Forgot Password?</a></p>
    <p><a href="/register">Don't have an account? Register here</a></p>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>

