<?php
$isEdit = isset($devotion);
$title = $isEdit ? 'Edit Devotion' : 'Create Devotion';
ob_start();
?>

<div class="admin-page">
    <h2><?= $isEdit ? 'Edit' : 'Create' ?> Devotion</h2>
    
    <form method="POST" action="<?= $isEdit ? "/admin/devotions/{$devotion['id']}" : '/admin/devotions' ?>">
        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
        <?php if ($isEdit): ?>
            <input type="hidden" name="_method" value="PUT">
        <?php endif; ?>
        
        <div class="form-group">
            <label for="devotion_date">Date:</label>
            <input type="date" id="devotion_date" name="devotion_date" 
                   value="<?= $isEdit ? escapeHtml($devotion['devotion_date']) : '' ?>" required>
        </div>
        
        <div class="form-group">
            <label>Readings:</label>
            <div class="readings-container">
                <div class="reading-item">
                    <label for="reading_old_testament">Old Testament:</label>
                    <input type="text" id="reading_old_testament" name="reading_old_testament" 
                           value="<?= $isEdit && isset($readings['Old Testament']) ? escapeHtml($readings['Old Testament']['scripture_reference']) : '' ?>" 
                           placeholder="e.g., Genesis 1:1-2:25">
                </div>
                <div class="reading-item">
                    <label for="reading_new_testament">New Testament:</label>
                    <input type="text" id="reading_new_testament" name="reading_new_testament" 
                           value="<?= $isEdit && isset($readings['New Testament']) ? escapeHtml($readings['New Testament']['scripture_reference']) : '' ?>" 
                           placeholder="e.g., Matthew 1:1-2:12">
                </div>
                <div class="reading-item">
                    <label for="reading_psalms">Psalms:</label>
                    <input type="text" id="reading_psalms" name="reading_psalms" 
                           value="<?= $isEdit && isset($readings['Psalms']) ? escapeHtml($readings['Psalms']['scripture_reference']) : '' ?>" 
                           placeholder="e.g., Psalm 1:1-6">
                </div>
                <div class="reading-item">
                    <label for="reading_proverbs">Proverbs:</label>
                    <input type="text" id="reading_proverbs" name="reading_proverbs" 
                           value="<?= $isEdit && isset($readings['Proverbs']) ? escapeHtml($readings['Proverbs']['scripture_reference']) : '' ?>" 
                           placeholder="e.g., Proverbs 1:1-6">
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <label for="scripture_references">Scripture References (comma-separated):</label>
            <textarea id="scripture_references" name="scripture_references" rows="2"><?= $isEdit ? escapeHtml($devotion['scripture_references'] ?? '') : '' ?></textarea>
        </div>
        
        <div class="form-group">
            <label for="verse_text">Verse Text (optional, requires permission):</label>
            <textarea id="verse_text" name="verse_text" rows="4"><?= $isEdit ? escapeHtml($devotion['verse_text'] ?? '') : '' ?></textarea>
        </div>
        
        <div class="form-group">
            <label>
                <input type="checkbox" name="author_paragraphs_enabled" value="1" 
                       <?= ($isEdit && $devotion['author_paragraphs_enabled']) ? 'checked' : '' ?>>
                Enable Author Paragraphs (requires permission)
            </label>
        </div>
        
        <button type="submit"><?= $isEdit ? 'Update' : 'Create' ?> Devotion</button>
        <a href="/admin/devotions">Cancel</a>
    </form>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>

