<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Scout Dashboard - Travel Guide</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group select, .form-group textarea { 
            width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; 
        }
        .form-group input.error, .form-group select.error, .form-group textarea.error {
            border-color: #dc3545;
            background-color: #fff8f8;
        }
        .alert { padding: 10px; margin-bottom: 20px; border-radius: 4px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .error-text { color: #dc3545; font-size: 12px; margin-top: 5px; display: block; }
        .post-list { margin-top: 30px; }
        .post-item { border: 1px solid #ddd; padding: 10px; margin-bottom: 10px; border-radius: 4px; }
        .status-pending { color: orange; }
        .status-approved { color: green; }
        .status-rejected { color: red; }
    </style>
</head>
<body>
    <nav class="nav">
        <a href="index.php?page=scout">Scout Dashboard</a>
        <a href="index.php?page=scoutrequests">My Requests</a>
        <a href="index.php?page=scoutapprovedposts">Approved Posts</a>
        <a href="index.php?page=logout">Logout</a>
    </nav>
    <div class="container">
        <h1>Scout Dashboard</h1>
        
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <h2>Create New Post Request</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label>Title *</label>
                <input type="text" name="title" value="<?= htmlspecialchars($old_input['title'] ?? '') ?>" class="<?= isset($errors['title']) ? 'error' : '' ?>">
                <?php if (isset($errors['title'])): ?>
                    <span class="error-text"><?= htmlspecialchars($errors['title']) ?></span>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label>Short History / Cultural Significance *</label>
                <textarea name="short_history" rows="4" class="<?= isset($errors['short_history']) ? 'error' : '' ?>"><?= htmlspecialchars($old_input['short_history'] ?? '') ?></textarea>
                <?php if (isset($errors['short_history'])): ?>
                    <span class="error-text"><?= htmlspecialchars($errors['short_history']) ?></span>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label>Country *</label>
                <input type="text" name="country" value="<?= htmlspecialchars($old_input['country'] ?? '') ?>" class="<?= isset($errors['country']) ? 'error' : '' ?>">
                <?php if (isset($errors['country'])): ?>
                    <span class="error-text"><?= htmlspecialchars($errors['country']) ?></span>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label>Genre *</label>
                <select name="genre" class="<?= isset($errors['genre']) ? 'error' : '' ?>">
                    <option value="">Select Genre</option>
                    <option value="beach" <?= ($old_input['genre'] ?? '') == 'beach' ? 'selected' : '' ?>>Beach</option>
                    <option value="mountain" <?= ($old_input['genre'] ?? '') == 'mountain' ? 'selected' : '' ?>>Mountain</option>
                    <option value="city" <?= ($old_input['genre'] ?? '') == 'city' ? 'selected' : '' ?>>City</option>
                    <option value="historical" <?= ($old_input['genre'] ?? '') == 'historical' ? 'selected' : '' ?>>Historical</option>
                    <option value="forest" <?= ($old_input['genre'] ?? '') == 'forest' ? 'selected' : '' ?>>Forest</option>
                    <option value="desert" <?= ($old_input['genre'] ?? '') == 'desert' ? 'selected' : '' ?>>Desert</option>
                </select>
                <?php if (isset($errors['genre'])): ?>
                    <span class="error-text"><?= htmlspecialchars($errors['genre']) ?></span>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label>Cost Level *</label>
                <select name="cost_level" class="<?= isset($errors['cost_level']) ? 'error' : '' ?>">
                    <option value="">Select Cost Level</option>
                    <option value="low" <?= ($old_input['cost_level'] ?? '') == 'low' ? 'selected' : '' ?>>Low</option>
                    <option value="medium" <?= ($old_input['cost_level'] ?? '') == 'medium' ? 'selected' : '' ?>>Medium</option>
                    <option value="high" <?= ($old_input['cost_level'] ?? '') == 'high' ? 'selected' : '' ?>>High</option>
                </select>
                <?php if (isset($errors['cost_level'])): ?>
                    <span class="error-text"><?= htmlspecialchars($errors['cost_level']) ?></span>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label>Travel Medium Info (e.g., flight, train, bus) *</label>
                <input type="text" name="travel_medium_info" value="<?= htmlspecialchars($old_input['travel_medium_info'] ?? '') ?>" class="<?= isset($errors['travel_medium_info']) ? 'error' : '' ?>">
                <?php if (isset($errors['travel_medium_info'])): ?>
                    <span class="error-text"><?= htmlspecialchars($errors['travel_medium_info']) ?></span>
                <?php endif; ?>
            </div>
            
            <button type="submit" name="submit_post_request">Submit Post Request</button>
        </form>
        

    </div>
</body>
</html>