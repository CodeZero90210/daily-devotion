<?php
$title = 'Admin - Users';
ob_start();
?>

<div class="admin-page">
    <h2>Manage Users</h2>
    
    <table class="admin-table">
        <thead>
            <tr>
                <th>Email</th>
                <th>Display Name</th>
                <th>Role</th>
                <th>Created</th>
                <th>Last Login</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= escapeHtml($user['email']) ?></td>
                    <td><?= escapeHtml($user['display_name']) ?></td>
                    <td>
                        <select class="role-select" data-user-id="<?= $user['id'] ?>">
                            <option value="site_pastor" <?= $user['role'] === 'site_pastor' ? 'selected' : '' ?>>Site Pastor</option>
                            <option value="brother" <?= $user['role'] === 'brother' ? 'selected' : '' ?>>Brother</option>
                            <option value="sister" <?= $user['role'] === 'sister' ? 'selected' : '' ?>>Sister</option>
                        </select>
                    </td>
                    <td><?= escapeHtml($user['created_at']) ?></td>
                    <td><?= escapeHtml($user['last_login_at'] ?? 'Never') ?></td>
                    <td>-</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
document.querySelectorAll('.role-select').forEach(select => {
    select.addEventListener('change', function() {
        const userId = this.dataset.userId;
        const role = this.value;
        
        const formData = new FormData();
        formData.append('csrf_token', '<?= $csrfToken ?>');
        formData.append('role', role);
        
        fetch(`/admin/users/${userId}/role`, {
            method: 'PUT',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Role updated successfully');
            } else {
                alert('Error: ' + (data.error || 'Failed to update role'));
                location.reload();
            }
        })
        .catch(error => {
            alert('Error: ' + error);
            location.reload();
        });
    });
});
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>

