<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Requests - Scout Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .requests-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .requests-table th, .requests-table td { 
            border: 1px solid #ddd; 
            padding: 12px; 
            text-align: left; 
            vertical-align: top;
        }
        .requests-table th { 
            background-color: #f4f4f4; 
            font-weight: bold;
        }
        .requests-table tr:hover { background-color: #f9f9f9; }
        .status-pending { color: orange; font-weight: bold; }
        .status-approved { color: green; font-weight: bold; }
        .status-rejected { color: red; font-weight: bold; }
        .btn { 
            padding: 5px 10px; 
            margin: 0 2px; 
            cursor: pointer; 
            text-decoration: none; 
            border: none; 
            border-radius: 3px; 
            display: inline-block;
            font-size: 12px;
        }
        .btn-edit { background: #007bff; color: white; }
        .btn-delete { background: #dc3545; color: white; }
        .btn-update { background: #28a745; color: white; }
        .btn-cancel { background: #6c757d; color: white; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group select, .form-group textarea { 
            width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; 
        }
        .form-group input.error, .form-group select.error, .form-group textarea.error {
            border-color: #dc3545;
            background-color: #fff8f8;
        }
        .error-text { color: #dc3545; font-size: 12px; margin-top: 5px; display: block; }
        .alert { padding: 10px; margin-bottom: 20px; border-radius: 4px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .edit-form { background: #f9f9f9; padding: 20px; margin-bottom: 20px; border-radius: 5px; }
        .nav { margin-bottom: 20px; }
        .nav a { margin-right: 15px; }
        .actions-cell { white-space: nowrap; width: 120px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="nav">
            <a href="index.php?page=scout">Scout Dashboard</a>
            <a href="index.php?page=scoutrequests">My Requests</a>
            <a href="index.php?page=scoutapprovedposts">Approved Posts</a>
            <a href="index.php?page=logout">Logout</a>
        </div>
        
        <h1>My Post Requests</h1>
        
        <?php if (isset($message) && $message): ?>
            <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        
        <?php if (isset($error) && $error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if ($editRequest): 
            $data = json_decode($editRequest['post_data'], true);
            $edit_errors = $edit_errors ?? [];
        ?>
            <div class="edit-form">
                <h2>Edit Request #<?= $editRequest['id'] ?></h2>
                <form method="POST" action="">
                    <input type="hidden" name="request_id" value="<?= $editRequest['id'] ?>">
                    
                    <div class="form-group">
                        <label>Title *</label>
                        <input type="text" name="title" value="<?= htmlspecialchars($_POST['title'] ?? $data['title'] ?? '') ?>" class="<?= isset($edit_errors['title']) ? 'error' : '' ?>">
                        <?php if (isset($edit_errors['title'])): ?>
                            <span class="error-text"><?= htmlspecialchars($edit_errors['title']) ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label>Short History / Cultural Significance *</label>
                        <textarea name="short_history" rows="4" class="<?= isset($edit_errors['short_history']) ? 'error' : '' ?>"><?= htmlspecialchars($_POST['short_history'] ?? $data['short_history'] ?? '') ?></textarea>
                        <?php if (isset($edit_errors['short_history'])): ?>
                            <span class="error-text"><?= htmlspecialchars($edit_errors['short_history']) ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label>Country *</label>
                        <input type="text" name="country" value="<?= htmlspecialchars($_POST['country'] ?? $data['country'] ?? '') ?>" class="<?= isset($edit_errors['country']) ? 'error' : '' ?>">
                        <?php if (isset($edit_errors['country'])): ?>
                            <span class="error-text"><?= htmlspecialchars($edit_errors['country']) ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label>Genre *</label>
                        <select name="genre" class="<?= isset($edit_errors['genre']) ? 'error' : '' ?>">
                            <option value="">Select Genre</option>
                            <option value="beach" <?= (($_POST['genre'] ?? $data['genre'] ?? '') == 'beach') ? 'selected' : '' ?>>Beach</option>
                            <option value="mountain" <?= (($_POST['genre'] ?? $data['genre'] ?? '') == 'mountain') ? 'selected' : '' ?>>Mountain</option>
                            <option value="city" <?= (($_POST['genre'] ?? $data['genre'] ?? '') == 'city') ? 'selected' : '' ?>>City</option>
                            <option value="historical" <?= (($_POST['genre'] ?? $data['genre'] ?? '') == 'historical') ? 'selected' : '' ?>>Historical</option>
                            <option value="forest" <?= (($_POST['genre'] ?? $data['genre'] ?? '') == 'forest') ? 'selected' : '' ?>>Forest</option>
                            <option value="desert" <?= (($_POST['genre'] ?? $data['genre'] ?? '') == 'desert') ? 'selected' : '' ?>>Desert</option>
                        </select>
                        <?php if (isset($edit_errors['genre'])): ?>
                            <span class="error-text"><?= htmlspecialchars($edit_errors['genre']) ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label>Cost Level *</label>
                        <select name="cost_level" class="<?= isset($edit_errors['cost_level']) ? 'error' : '' ?>">
                            <option value="">Select Cost Level</option>
                            <option value="low" <?= (($_POST['cost_level'] ?? $data['cost_level'] ?? '') == 'low') ? 'selected' : '' ?>>Low</option>
                            <option value="medium" <?= (($_POST['cost_level'] ?? $data['cost_level'] ?? '') == 'medium') ? 'selected' : '' ?>>Medium</option>
                            <option value="high" <?= (($_POST['cost_level'] ?? $data['cost_level'] ?? '') == 'high') ? 'selected' : '' ?>>High</option>
                        </select>
                        <?php if (isset($edit_errors['cost_level'])): ?>
                            <span class="error-text"><?= htmlspecialchars($edit_errors['cost_level']) ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label>Travel Medium Info *</label>
                        <input type="text" name="travel_medium_info" value="<?= htmlspecialchars($_POST['travel_medium_info'] ?? $data['travel_medium_info'] ?? '') ?>" class="<?= isset($edit_errors['travel_medium_info']) ? 'error' : '' ?>">
                        <?php if (isset($edit_errors['travel_medium_info'])): ?>
                            <span class="error-text"><?= htmlspecialchars($edit_errors['travel_medium_info']) ?></span>
                        <?php endif; ?>
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
            <table class="requests-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Country</th>
                        <th>Genre</th>
                        <th>Cost Level</th>
                        <th>Travel Medium</th>
                        <th>Status</th>
                        <th>Submitted</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($requests as $request): 
                        $data = json_decode($request['post_data'], true);
                    ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($data['title'] ?? 'N/A') ?></strong></td>
                            <td><?= htmlspecialchars($data['country'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($data['genre'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($data['cost_level'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($data['travel_medium_info'] ?? 'N/A') ?></td>
                            <td><span class="status-<?= $request['status'] ?>"><?= $request['status'] ?></span></td>
                            <td><?= $request['requested_at'] ?></td>
                            <td class="actions-cell">
                                <?php if ($request['status'] === 'pending'): ?>
                                    <a href="index.php?page=scoutrequests&edit=<?= $request['id'] ?>" class="btn btn-edit">Edit</a>
                                    <form method="POST" action="" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this request?')">
                                        <input type="hidden" name="request_id" value="<?= $request['id'] ?>">
                                        <button type="submit" name="delete_request" class="btn btn-delete">Delete</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>