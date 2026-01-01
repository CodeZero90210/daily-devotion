<?php
/**
 * Recursive comment tree rendering
 */
function renderComment($comment, $user, $csrfToken, $editWindowMinutes) {
    $canEdit = ($comment['user_id'] == $user['id'] && 
                (time() - strtotime($comment['created_at'])) <= ($editWindowMinutes * 60));
    $canDelete = ($comment['user_id'] == $user['id'] || $user['role'] === 'site_pastor');
    $canReply = $comment['depth'] < 2;
    $timeAgo = getTimeAgo($comment['created_at']);
    $edited = !empty($comment['edited_at']);
    ?>
    
    <div class="comment comment-depth-<?= $comment['depth'] ?>" data-comment-id="<?= $comment['id'] ?>">
        <div class="comment-header">
            <strong><?= escapeHtml($comment['display_name']) ?></strong>
            <span class="role">(<?= escapeHtml(User::getRoleLabel($comment['role'])) ?>)</span>
            <span class="time"><?= $timeAgo ?></span>
            <?php if ($edited): ?>
                <span class="edited">(edited)</span>
            <?php endif; ?>
        </div>
        
        <div class="comment-body">
            <?= formatCommentBody($comment['body']) ?>
        </div>
        
        <div class="comment-actions">
            <?php if ($canReply): ?>
                <button class="reply-btn" data-parent-id="<?= $comment['id'] ?>">Reply</button>
            <?php endif; ?>
            <?php if ($canEdit): ?>
                <button class="edit-btn" data-comment-id="<?= $comment['id'] ?>">Edit</button>
            <?php endif; ?>
            <?php if ($canDelete): ?>
                <button class="delete-btn" data-comment-id="<?= $comment['id'] ?>">Delete</button>
            <?php endif; ?>
        </div>
        
        <?php if (!empty($comment['children'])): ?>
            <div class="comment-children">
                <?php foreach ($comment['children'] as $child): ?>
                    <?php renderComment($child, $user, $csrfToken, $editWindowMinutes); ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <?php
}

function getTimeAgo($datetime) {
    $time = time() - strtotime($datetime);
    
    if ($time < 60) return 'just now';
    if ($time < 3600) return floor($time / 60) . ' minutes ago';
    if ($time < 86400) return floor($time / 3600) . ' hours ago';
    if ($time < 2592000) return floor($time / 86400) . ' days ago';
    return date('M j, Y', strtotime($datetime));
}

foreach ($comments as $comment) {
    renderComment($comment, $user, $csrfToken, $editWindowMinutes);
}
?>

