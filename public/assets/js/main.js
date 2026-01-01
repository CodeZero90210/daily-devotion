// Comment functionality
document.addEventListener('DOMContentLoaded', function() {
    const commentForm = document.getElementById('comment-form');
    const commentsContainer = document.getElementById('comments-container');
    const cancelReplyBtn = document.getElementById('cancel-reply');
    const parentCommentIdInput = document.getElementById('parent_comment_id');
    const commentBodyTextarea = document.getElementById('comment-body');
    
    // Reply button handler
    if (commentsContainer) {
        commentsContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('reply-btn')) {
                const parentId = e.target.dataset.parentId;
                parentCommentIdInput.value = parentId;
                cancelReplyBtn.style.display = 'inline-block';
                commentBodyTextarea.focus();
                commentBodyTextarea.scrollIntoView({ behavior: 'smooth' });
            }
        });
    }
    
    // Cancel reply
    if (cancelReplyBtn) {
        cancelReplyBtn.addEventListener('click', function() {
            parentCommentIdInput.value = '';
            cancelReplyBtn.style.display = 'none';
        });
    }
    
    // Comment form submission
    if (commentForm) {
        commentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(commentForm);
            
            fetch('/api/comments', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + (data.error || 'Failed to post comment'));
                }
            })
            .catch(error => {
                alert('Error: ' + error);
            });
        });
    }
    
    // Edit comment
    if (commentsContainer) {
        commentsContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('edit-btn')) {
                const commentId = e.target.dataset.commentId;
                const commentElement = e.target.closest('.comment');
                const commentBody = commentElement.querySelector('.comment-body');
                const currentText = commentBody.textContent.trim();
                
                const textarea = document.createElement('textarea');
                textarea.value = currentText;
                textarea.rows = 4;
                textarea.style.width = '100%';
                
                const saveBtn = document.createElement('button');
                saveBtn.textContent = 'Save';
                saveBtn.type = 'button';
                
                const cancelBtn = document.createElement('button');
                cancelBtn.textContent = 'Cancel';
                cancelBtn.type = 'button';
                
                commentBody.innerHTML = '';
                commentBody.appendChild(textarea);
                
                const actions = commentElement.querySelector('.comment-actions');
                actions.innerHTML = '';
                actions.appendChild(saveBtn);
                actions.appendChild(cancelBtn);
                
                saveBtn.addEventListener('click', function() {
                    const newBody = textarea.value.trim();
                    if (!newBody) {
                        alert('Comment cannot be empty');
                        return;
                    }
                    
                    const formData = new FormData();
                    formData.append('csrf_token', csrfToken);
                    formData.append('body', newBody);
                    
                    fetch(`/api/comments/${commentId}`, {
                        method: 'PUT',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Error: ' + (data.error || 'Failed to update comment'));
                            location.reload();
                        }
                    })
                    .catch(error => {
                        alert('Error: ' + error);
                        location.reload();
                    });
                });
                
                cancelBtn.addEventListener('click', function() {
                    location.reload();
                });
            }
        });
    }
    
    // Delete comment
    if (commentsContainer) {
        commentsContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('delete-btn')) {
                if (!confirm('Are you sure you want to delete this comment?')) {
                    return;
                }
                
                const commentId = e.target.dataset.commentId;
                
                const formData = new FormData();
                formData.append('csrf_token', csrfToken);
                
                fetch(`/api/comments/${commentId}`, {
                    method: 'DELETE',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + (data.error || 'Failed to delete comment'));
                    }
                })
                .catch(error => {
                    alert('Error: ' + error);
                });
            }
        });
    }
});

