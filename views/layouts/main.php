<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? escapeHtml($title) : 'Daily Devotion' ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1><a href="/">Daily Devotion</a></h1>
            <nav>
                <?php if (isAuthenticated()): ?>
                    <?php $user = getCurrentUser(); ?>
                    <span>Welcome, <?= escapeHtml($user['display_name']) ?> (<?= escapeHtml(User::getRoleLabel($user['role'])) ?>)</span>
                    <?php if ($user['role'] === 'site_pastor'): ?>
                        <a href="/admin/devotions">Admin</a>
                    <?php endif; ?>
                    <form method="POST" action="/logout" style="display: inline;">
                        <input type="hidden" name="csrf_token" value="<?= getCSRFToken() ?>">
                        <button type="submit">Logout</button>
                    </form>
                <?php else: ?>
                    <a href="/login">Login</a>
                    <a href="/register">Register</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>
    
    <main class="container">
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?= escapeHtml($_SESSION['error']) ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?= escapeHtml($_SESSION['success']) ?>
                <?php unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        
        <?= $content ?? '' ?>
    </main>
    
    <footer>
        <div class="container">
            <p>&copy; <?= date('Y') ?> Daily Devotion</p>
        </div>
    </footer>
    
    <script src="/assets/js/main.js"></script>
</body>
</html>

