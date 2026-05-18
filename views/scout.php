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
        .alert { padding: 10px; margin-bottom: 20px; border-radius: 4px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .post-list { margin-top: 30px; }
        .post-item { border: 1px solid #ddd; padding: 10px; margin-bottom: 10px; border-radius: 4px; }
        .status-pending { color: orange; }
        .status-approved { color: green; }
        .status-rejected { color: red; }
    </style>
</head>
<body>
    <nav>
        <a href="index.php?page=scout">Scout Dashboard</a>
        <a href="index.php?page=profile">Profile</a>
        <a href="index.php?page=scoutrequests">My Requests</a>
        <a href="index.php?page=logout">Logout</a>
    </nav>
    <div class="container">
        <h1>Scout Dashboard</h1>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <h2>Create New Post Request</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label>Title *</label>
                <input type="text" name="title" required>
            </div>
            
            <div class="form-group">
                <label>Short History / Cultural Significance *</label>
                <textarea name="short_history" rows="4" required></textarea>
            </div>
            
            <div class="form-group">
                <label>Country *</label>
                <input type="text" name="country" required>
            </div>
            
            <div class="form-group">
                <label>Genre *</label>
                <select name="genre" required>
                    <option value="">Select Genre</option>
                    <option value="beach">Beach</option>
                    <option value="mountain">Mountain</option>
                    <option value="city">City</option>
                    <option value="historical">Historical</option>
                    <option value="forest">Forest</option>
                    <option value="desert">Desert</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Cost Level *</label>
                <select name="cost_level" required>
                    <option value="">Select Cost Level</option>
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Travel Medium Info (e.g., flight, train, bus) *</label>
                <input type="text" name="travel_medium_info" required>
            </div>
            
            <button type="submit" name="submit_post_request">Submit Post Request</button>
        </form>
        
        <div class="post-list">
            <h2>Your Submitted Requests</h2>
            <?php if (empty($requests) && empty($posts)): ?>
                <p>No requests submitted yet.</p>
            <?php else: ?>
                <?php foreach($requests as $req): 
                    $data = json_decode($req['post_data'], true);
                ?>
                    <div class="post-item">
                        <strong><?= htmlspecialchars($data['title'] ?? 'N/A') ?></strong><br>
                        Country: <?= htmlspecialchars($data['country'] ?? 'N/A') ?><br>
                        Genre: <?= htmlspecialchars($data['genre'] ?? 'N/A') ?><br>
                        Status: <span class="status-<?= $req['status'] ?>"><?= $req['status'] ?></span><br>
                        Submitted: <?= $req['requested_at'] ?>
                    </div>
                <?php endforeach; ?>
                
                <?php foreach($posts as $post): ?>
                    <div class="post-item">
                        <strong><?= htmlspecialchars($post['title']) ?></strong><br>
                        Country: <?= htmlspecialchars($post['country']) ?><br>
                        Genre: <?= htmlspecialchars($post['genre']) ?><br>
                        Status: <span class="status-<?= $post['status'] ?>"><?= $post['status'] ?></span><br>
                        Created: <?= $post['created_at'] ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>