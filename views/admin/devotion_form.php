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
        
        <div class="form-group">
            <label>Readings:</label>
            <div id="readings-container">
                <?php
                $existingReadings = $isEdit ? $readings : [];
                $readingsCount = max(count($existingReadings), 3);
                for ($i = 0; $i < $readingsCount; $i++):
                ?>
                    <div class="reading-item">
                        <input type="text" name="readings[]" 
                               value="<?= isset($existingReadings[$i]) ? escapeHtml($existingReadings[$i]['scripture_reference']) : '' ?>" 
                               placeholder="e.g., John 3:16-17">
                    </div>
                <?php endfor; ?>
            </div>
            <button type="button" id="add-reading">Add Another Reading</button>
        </div>
        
        <button type="submit"><?= $isEdit ? 'Update' : 'Create' ?> Devotion</button>
        <a href="/admin/devotions">Cancel</a>
    </form>
</div>

<script>
document.getElementById('add-reading').addEventListener('click', function() {
    const container = document.getElementById('readings-container');
    const div = document.createElement('div');
    div.className = 'reading-item';
    div.innerHTML = '<input type="text" name="readings[]" placeholder="e.g., John 3:16-17">';
    container.appendChild(div);
});
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>

