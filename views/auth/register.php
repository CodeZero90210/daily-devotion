<?php
$title = 'Register';
ob_start();
?>

<div class="auth-form">
    <h2>Register</h2>
    <form method="POST" action="/register">
        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
        
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        
        <div class="form-group">
            <label for="display_name">Display Name:</label>
            <input type="text" id="display_name" name="display_name" required>
        </div>
        
        <div class="form-group">
            <label for="password">Password (min 12 characters):</label>
            <input type="password" id="password" name="password" required minlength="12">
        </div>
        
        <div class="form-group">
            <label for="role">Role:</label>
            <select id="role" name="role">
                <option value="brother">Brother</option>
                <option value="sister">Sister</option>
            </select>
        </div>
        
        <button type="submit">Register</button>
    </form>
    
    <p><a href="/login">Already have an account? Login here</a></p>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>

