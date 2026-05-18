<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Requests - Scout Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .request-card { border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; border-radius: 5px; }
        .status-pending { color: orange; font-weight: bold; }
        .status-approved { color: green; font-weight: bold; }
        .status-rejected { color: red; font-weight: bold; }
        .btn { padding: 5px 10px; margin: 0 5px; cursor: pointer; text-decoration: none; border: none; border-radius: 3px; }
        .btn-edit { background: #007bff; color: white; }
        .btn-delete { background: #dc3545; color: white; }
        .btn-update { background: #28a745; color: white; }
        .btn-cancel { background: #6c757d; color: white; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        .alert { padding: 10px; margin-bottom: 20px; border-radius: 4px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .edit-form { background: #f9f9f9; padding: 20px; margin-bottom: 20px; border-radius: 5px; }
        .nav { margin-bottom: 20px; }
        .nav a { margin-right: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="nav">
            <a href="index.php?page=scout">Scout Dashboard</a>
            <a href="index.php?page=scoutrequests">My Requests</a>
            <a href="index.php?page=profile">Profile</a>
            <a href="index.php?page=logout">Logout</a>
        </div>
        
        <h1>My Post Requests</h1>
        
        <?php if ($message): ?>
            <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if ($editRequest): 
            $data = json_decode($editRequest['post_data'], true);
        ?>
            <div class="edit-form">
                <h2>Edit Request #<?= $editRequest['id'] ?></h2>
                <form method="POST" action="">
                    <input type="hidden" name="request_id" value="<?= $editRequest['id'] ?>">
                    
                    <div class="form-group">
                        <label>Title *</label>
                        <input type="text" name="title" value="<?= htmlspecialchars($data['title'] ?? '') ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Short History / Cultural Significance *</label>
                        <textarea name="short_history" rows="4" required><?= htmlspecialchars($data['short_history'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Country *</label>
                        <input type="text" name="country" value="<?= htmlspecialchars($data['country'] ?? '') ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Genre *</label>
                        <select name="genre" required>
                            <option value="">Select Genre</option>
                            <option value="beach" <?= ($data['genre'] ?? '') == 'beach' ? 'selected' : '' ?>>Beach</option>
                            <option value="mountain" <?= ($data['genre'] ?? '') == 'mountain' ? 'selected' : '' ?>>Mountain</option>
                            <option value="city" <?= ($data['genre'] ?? '') == 'city' ? 'selected' : '' ?>>City</option>
                            <option value="historical" <?= ($data['genre'] ?? '') == 'historical' ? 'selected' : '' ?>>Historical</option>
                            <option value="forest" <?= ($data['genre'] ?? '') == 'forest' ? 'selected' : '' ?>>Forest</option>
                            <option value="desert" <?= ($data['genre'] ?? '') == 'desert' ? 'selected' : '' ?>>Desert</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Cost Level *</label>
                        <select name="cost_level" required>
                            <option value="">Select Cost Level</option>
                            <option value="low" <?= ($data['cost_level'] ?? '') == 'low' ? 'selected' : '' ?>>Low</option>
                            <option value="medium" <?= ($data['cost_level'] ?? '') == 'medium' ? 'selected' : '' ?>>Medium</option>
                            <option value="high" <?= ($data['cost_level'] ?? '') == 'high' ? 'selected' : '' ?>>High</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Travel Medium Info *</label>
                        <input type="text" name="travel_medium_info" value="<?= htmlspecialchars($data['travel_medium_info'] ?? '') ?>" required>
                    </div>
                    
                    <button type="submit" name="update_request" class="btn btn-update">Update Request</button>
                    <a href="index.php?page=scoutrequests" class="btn btn-cancel">Cancel</a>
                </form>
            </div>
        <?php endif; ?>
        
        <h2>All Requests</h2>
        <?php if (empty($requests)): ?>
            <p>No post requests found.</p>
        <?php else: ?>
            <?php foreach($requests as $request): 
                $data = json_decode($request['post_data'], true);
            ?>
                <div class="request-card">
                    <h3><?= htmlspecialchars($data['title'] ?? 'N/A') ?></h3>
                    <p><strong>Country:</strong> <?= htmlspecialchars($data['country'] ?? 'N/A') ?></p>
                    <p><strong>Genre:</strong> <?= htmlspecialchars($data['genre'] ?? 'N/A') ?></p>
                    <p><strong>Cost Level:</strong> <?= htmlspecialchars($data['cost_level'] ?? 'N/A') ?></p>
                    <p><strong>Travel Medium:</strong> <?= htmlspecialchars($data['travel_medium_info'] ?? 'N/A') ?></p>
                    <p><strong>Status:</strong> <span class="status-<?= $request['status'] ?>"><?= $request['status'] ?></span></p>
                    <p><strong>Submitted:</strong> <?= $request['requested_at'] ?></p>
                    
                    <?php if ($request['status'] === 'pending'): ?>
                        <a href="index.php?page=scoutrequests&edit=<?= $request['id'] ?>" class="btn btn-edit">Edit</a>
                        <form method="POST" action="" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this request?')">
                            <input type="hidden" name="request_id" value="<?= $request['id'] ?>">
                            <button type="submit" name="delete_request" class="btn btn-delete">Delete</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>