<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Approved Posts - Scout Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <style>
    .nav {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    width: 100%;
    background-color: #1900ff;
    padding: 1rem 2rem;
    margin: 0;
    display: flex;
    gap: 2rem;
    z-index: 1000;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    box-sizing: border-box;
}

.nav a {
    color: white;
    text-decoration: none;
    padding: 0.5rem 1rem;
    transition: background-color 0.3s ease;
    border-radius: 4px;
}

.nav a:hover {
    background-color: #555;
}

body {
    margin: 0;
    padding-top: 70px; 
    background: #3498db;
}

.container {
    max-width: 90%;
    margin: 0 auto;
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    border: 1px solid #e0e0e0;
}
        .posts-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .posts-table th, .posts-table td { 
            border: 1px solid #ddd; 
            padding: 12px; 
            text-align: left; 
            vertical-align: top;
        }
        .posts-table th { 
            background-color: #f4f4f4; 
            font-weight: bold;
        }
        .posts-table tr:hover { background-color: #f9f9f9; }
        .post-title { font-weight: bold; font-size: 1.1em; }
        .btn { 
            padding: 5px 10px; 
            margin: 2px; 
            cursor: pointer; 
            border: none; 
            border-radius: 3px; 
            display: inline-block;
            font-size: 12px;
        }
        .btn-change { background: #ffc107; color: #000; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group select, .form-group textarea { 
            width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; 
        }
        .edit-form { background: #f9f9f9; padding: 20px; margin-top: 15px; border-radius: 5px; }
        .alert { padding: 10px; margin-bottom: 20px; border-radius: 4px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .nav { margin-bottom: 20px; }
        .nav a { margin-right: 15px; }
        .actions-cell { white-space: nowrap; width: 130px; }
        .short-history-cell { max-width: 300px; }
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 20px;
            border-radius: 4px;
            z-index: 1000;
            display: none;
        }
        .toast-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .toast-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <div class="container">
        <div id="toast" class="toast"></div>
        
        <div class="nav">
            <a href="index.php?page=scout">Scout Dashboard</a>
            <a href="index.php?page=scoutrequests">My Requests</a>
            <a href="index.php?page=scoutapprovedposts">Approved Posts</a>
            <a href="index.php?page=logout">Logout</a>
        </div>
        
        <h1>Approved Travel Posts</h1>
        
        <?php if (empty($posts)): ?>
            <p>No approved posts found.</p>
        <?php else: ?>
            <table class="posts-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Country</th>
                        <th>Genre</th>
                        <th>Cost Level</th>
                        <th>Travel Medium</th>
                        <th>Short History</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($posts as $post): ?>
                        <tr id="post-<?= $post['id'] ?>">
                            <td>
                                <div class="post-title"><?= htmlspecialchars($post['title']) ?></div>
                            </td>
                            <td><?= htmlspecialchars($post['country']) ?></td>
                            <td><?= htmlspecialchars($post['genre']) ?></td>
                            <td><?= htmlspecialchars($post['cost_level']) ?></td>
                            <td><?= htmlspecialchars($post['travel_medium_info']) ?></td>
                            <td class="short-history-cell"><?= htmlspecialchars(substr($post['short_history'], 0, 150)) ?>...</td>
                            <td class="actions-cell">
                                <button class="btn btn-change" onclick="toggleEditForm(<?= $post['id'] ?>)">Request Changes</button>
                            </td>
                        </tr>
                        <tr id="edit-row-<?= $post['id'] ?>" style="display: none;">
                            <td colspan="7">
                                <div class="edit-form">
                                    <h3>Request Changes for: <?= htmlspecialchars($post['title']) ?></h3>
                                    
                                    <div class="form-group">
                                        <label>Title *</label>
                                        <input type="text" id="title-<?= $post['id'] ?>" value="<?= htmlspecialchars($post['title']) ?>" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Short History / Cultural Significance *</label>
                                        <textarea id="history-<?= $post['id'] ?>" rows="4" required><?= htmlspecialchars($post['short_history']) ?></textarea>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Country *</label>
                                        <input type="text" id="country-<?= $post['id'] ?>" value="<?= htmlspecialchars($post['country']) ?>" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Genre *</label>
                                        <select id="genre-<?= $post['id'] ?>" required>
                                            <option value="beach" <?= $post['genre'] == 'beach' ? 'selected' : '' ?>>Beach</option>
                                            <option value="mountain" <?= $post['genre'] == 'mountain' ? 'selected' : '' ?>>Mountain</option>
                                            <option value="city" <?= $post['genre'] == 'city' ? 'selected' : '' ?>>City</option>
                                            <option value="historical" <?= $post['genre'] == 'historical' ? 'selected' : '' ?>>Historical</option>
                                            <option value="forest" <?= $post['genre'] == 'forest' ? 'selected' : '' ?>>Forest</option>
                                            <option value="desert" <?= $post['genre'] == 'desert' ? 'selected' : '' ?>>Desert</option>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Cost Level *</label>
                                        <select id="cost-<?= $post['id'] ?>" required>
                                            <option value="low" <?= $post['cost_level'] == 'low' ? 'selected' : '' ?>>Low</option>
                                            <option value="medium" <?= $post['cost_level'] == 'medium' ? 'selected' : '' ?>>Medium</option>
                                            <option value="high" <?= $post['cost_level'] == 'high' ? 'selected' : '' ?>>High</option>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Travel Medium Info *</label>
                                        <input type="text" id="travel-<?= $post['id'] ?>" value="<?= htmlspecialchars($post['travel_medium_info']) ?>" required>
                                    </div>
                                    
                                    <button class="btn btn-change" onclick="submitChangeRequest(<?= $post['id'] ?>)">Submit Change Request</button>
                                    <button type="button" class="btn" onclick="toggleEditForm(<?= $post['id'] ?>)">Cancel</button>
                                    
                                    <div id="loading-<?= $post['id'] ?>" style="display:none; margin-top:10px;">Submitting...</div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    
    <script>
        function toggleEditForm(postId) {
            var editRow = document.getElementById('edit-row-' + postId);
            if (editRow) {
                if (editRow.style.display === 'none' || editRow.style.display === '') {
                    editRow.style.display = 'table-row';
                } else {
                    editRow.style.display = 'none';
                }
            }
        }
        
        function showToast(message, type) {
            var toast = document.getElementById('toast');
            toast.className = 'toast toast-' + type;
            toast.innerHTML = message;
            toast.style.display = 'block';
            setTimeout(function() {
                toast.style.display = 'none';
            }, 3000);
        }
        
        function submitChangeRequest(postId) {
            // Get form values
            var title = document.getElementById('title-' + postId).value;
            var short_history = document.getElementById('history-' + postId).value;
            var country = document.getElementById('country-' + postId).value;
            var genre = document.getElementById('genre-' + postId).value;
            var cost_level = document.getElementById('cost-' + postId).value;
            var travel_medium_info = document.getElementById('travel-' + postId).value;
            
            // Validate
            if (!title || !short_history || !country || !genre || !cost_level || !travel_medium_info) {
                showToast('Please fill in all fields', 'error');
                return;
            }
            
            // Show loading
            document.getElementById('loading-' + postId).style.display = 'block';
            
            // Send AJAX request
            fetch('index.php?page=ajax&type=change-request', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    original_post_id: postId,
                    title: title,
                    short_history: short_history,
                    country: country,
                    genre: genre,
                    cost_level: cost_level,
                    travel_medium_info: travel_medium_info
                })
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('loading-' + postId).style.display = 'none';
                
                if (data.success) {
                    showToast('Change request submitted successfully! Waiting for admin approval.', 'success');
                    toggleEditForm(postId); // Hide the form
                } else {
                    showToast('Error: ' + (data.error || 'Failed to submit request'), 'error');
                }
            })
            .catch(error => {
                document.getElementById('loading-' + postId).style.display = 'none';
                showToast('Network error: ' + error, 'error');
            });
        }
    </script>
</body>
</html>