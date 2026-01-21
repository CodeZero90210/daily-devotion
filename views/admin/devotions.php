<?php
require_once __DIR__ . '/../../models/Reading.php';
$title = 'Admin - Devotions';
ob_start();
?>

<div class="admin-page">
    <h2>Manage Devotions</h2>
    
    <p><a href="/admin/devotions/new" class="btn btn-primary">Create New Devotion</a></p>
    
    <table class="admin-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Scripture References</th>
                <th>Readings</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($devotions as $devotion): ?>
                <tr>
                    <td><?= escapeHtml(formatDevotionDate($devotion['devotion_date'])) ?></td>
                    <td><?= escapeHtml($devotion['scripture_references'] ?? '') ?></td>
                    <td>
                        <?php
                        $readingModel = new Reading();
                        $readings = $readingModel->getByDevotionId($devotion['id']);
                        echo count($readings);
                        ?>
                    </td>
                    <td>
                        <a href="/devotion/date/<?= $devotion['devotion_date'] ?>">View</a>
                        <a href="/admin/devotions/<?= $devotion['id'] ?>/edit">Edit</a>
                        <form method="POST" action="/admin/devotions/<?= $devotion['id'] ?>/delete" style="display: inline;" onsubmit="return confirm('Are you sure?');">
                            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                            <button type="submit" class="btn-link">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>

