<?php
$title = 'Devotion - ' . date('F j, Y', strtotime($devotion['devotion_date']));
ob_start();
?>

<div class="devotion-page">
    <div class="devotion-header">
        <h2>Devotion for <?= date('F j, Y', strtotime($devotion['devotion_date'])) ?></h2>
    </div>
    
    <?php if (!empty($readings)): ?>
        <div class="readings">
            <h3>Today's Readings:</h3>
            <ol>
                <?php foreach ($readings as $reading): ?>
                    <li><?= escapeHtml($reading['scripture_reference']) ?></li>
                <?php endforeach; ?>
            </ol>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($devotion['scripture_references'])): ?>
        <div class="scripture-references">
            <h3>Scripture References:</h3>
            <p><?= escapeHtml($devotion['scripture_references']) ?></p>
        </div>
    <?php endif; ?>
    
    <?php if ($showText && !empty($paragraphs)): ?>
        <div class="paragraphs">
            <?php foreach ($paragraphs as $paragraph): ?>
                <?php if (!empty($paragraph['content'])): ?>
                    <p><?= nl2br(escapeHtml($paragraph['content'])) ?></p>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <hr>
    
    <div class="comments-section">
        <h3>Comments</h3>
        
        <div class="comment-form">
            <form id="comment-form" method="POST" action="/api/comments">
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                <input type="hidden" name="devotion_id" value="<?= $devotion['id'] ?>">
                <input type="hidden" name="parent_comment_id" id="parent_comment_id" value="">
                
                <div class="form-group">
                    <label for="comment-body">Your Comment:</label>
                    <textarea id="comment-body" name="body" rows="4" required></textarea>
                </div>
                
                <button type="submit">Post Comment</button>
                <button type="button" id="cancel-reply" style="display:none;">Cancel Reply</button>
            </form>
        </div>
        
        <div id="comments-container">
            <?php if (!empty($comments)): ?>
                <?php require __DIR__ . '/_comment_tree.php'; ?>
            <?php else: ?>
                <p>No comments yet. Be the first to comment!</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    const devotionId = <?= $devotion['id'] ?>;
    const csrfToken = '<?= $csrfToken ?>';
    const editWindowMinutes = <?= $appConfig['comment_edit_window_minutes'] ?>;
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>

