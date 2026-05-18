<?php
// Fallbacks to eliminate undefined variable warnings in VS Code
$post = $post ?? ['title' => '', 'genre' => '', 'country' => '', 'short_history' => '', 'travel_medium_info' => '', 'id' => 0, 'cost_level' => ''];
$comments = $comments ?? [];
$cost = $cost ?? ['base_cost' => 0, 'currency' => 'USD'];

function detailImage($genre)
{
    $g = strtolower(trim($genre));
    $allowed = ['beach', 'mountain', 'city', 'historical'];
    if (!in_array($g, $allowed, true)) $g = 'default';
    return 'images/' . $g . '.svg';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($post['title']); ?> - Travel Guide</title>
    <link rel="stylesheet" href="style.css">
</head>

<body class="app-body">
    <nav class="navbar">
        <div class="navbar-inner">
            <a class="brand" href="index.php?page=browse"><span class="brand-icon">✈️</span> Travel Guide</a>
            <div class="nav-user">
                <a class="btn btn-ghost" href="index.php?page=browse">Back</a>
                <?php if (isLoggedIn()): ?>
                    <a class="btn-logout" href="index.php?page=logout">Logout</a>
                <?php else: ?>
                    <a class="btn btn-primary" href="index.php?page=login">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <main class="main-content narrow">
        <section class="detail-hero-card">
            <img class="detail-img" src="<?php echo e(detailImage($post['genre'])); ?>" alt="<?php echo e($post['genre']); ?> image">
            <div class="detail-content">
                <div class="post-tags">
                    <span><?php echo e($post['country']); ?></span>
                    <span><?php echo e(ucfirst($post['genre'])); ?></span>
                    <span><?php echo e(ucfirst($post['cost_level'])); ?> Cost</span>
                </div>
                <h1><?php echo e($post['title']); ?></h1>
                <p class="lead-text"><?php echo nl2br(e($post['short_history'])); ?></p>
            </div>
        </section>

        <section class="info-grid">
            <div class="info-card">
                <h3>Travel Info</h3>
                <p><?php echo nl2br(e($post['travel_medium_info'])); ?></p>
            </div>
            <div class="info-card">
                <h3>Place Summary</h3>
                <p><strong>Country:</strong> <?php echo e($post['country']); ?></p>
                <p><strong>Country Representation:</strong> Cultural & Historical Significance of <?php echo e($post['country']); ?></p>
                <p><strong>Cost Level:</strong> <?php echo e(ucfirst($post['cost_level'])); ?></p>
            </div>
        </section>

        <?php if (isVerifiedGeneralUser()): ?>
            <section class="card form-card cost-card">
                <div class="section-heading">
                    <h2>Probable Cost Estimate</h2>
                    <p>Simple calculation for defense explanation.</p>
                </div>
                <p class="base-cost">Base cost: <strong id="baseCost"><?php echo e($cost['base_cost']); ?></strong> <?php echo e($cost['currency']); ?> per 7 days.</p>
                <div class="field-row">
                    <div class="field">
                        <label>Travelers (1-10)</label>
                        <input type="number" id="travelers" min="1" max="10" value="1">
                    </div>
                    <div class="field">
                        <label>Days</label>
                        <input type="number" id="days" min="1" value="7">
                    </div>
                </div>
                <button type="button" class="btn btn-primary" onclick="calculateCost()">Calculate Cost</button>
                <p id="costResult" class="result-text"></p>
            </section>
        <?php endif; ?>

        <section class="card form-card">
            <div class="section-heading">
                <h2>Comments</h2>
                <p>users can add and delete their own comments</p>
            </div>
            <div id="commentList" class="comment-list">
                <?php foreach ($comments as $comment): ?>
                    <div class="comment" id="comment-<?php echo intval($comment['id']); ?>">
                        <div class="comment-top">
                            <strong><?php echo e($comment['name']); ?></strong>
                            <small><?php echo e($comment['created_at']); ?></small>
                        </div>
                        <p><?php echo e($comment['content']); ?></p>
                        <?php if (isVerifiedGeneralUser() && intval($comment['user_id']) === intval($_SESSION['user']['id'])): ?>
                            <button class="btn-sm btn-delete" onclick="deleteComment(<?php echo intval($comment['id']); ?>)">Delete</button>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if (isVerifiedGeneralUser()): ?>
                <form id="commentForm" class="form comment-form">
                    <input type="hidden" id="postId" value="<?php echo intval($post['id']); ?>">
                    <div class="field">
                        <label>Name</label>
                        <input type="text" value="<?php echo e($_SESSION['user']['name']); ?>" readonly>
                    </div>
                    <div class="field">
                        <label>Comment</label>
                        <textarea id="commentContent" maxlength="500" placeholder="Write your comment"></textarea>
                    </div>
                    <p id="commentError" class="alert-text"></p>
                    <button class="btn btn-primary" type="submit">Post Comment</button>
                </form>
            <?php else: ?>
                <p class="hint">Only verified users can post comments and use cost estimate.</p>
            <?php endif; ?>
        </section>
    </main>

    <footer class="footer">@ Travel Guide. All rights reserved. </footer>

    <script>
        // 1. Safe HTML Escaping Helper function to prevent XSS
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text ?? '';
            return div.innerHTML;
        }

        // 2. FIXED: Cost Estimation Calculator Logic
        function calculateCost() {
            // Get values directly from the input fields by ID
            const baseCostElement = document.getElementById('baseCost');
            const travelersElement = document.getElementById('travelers');
            const daysElement = document.getElementById('days');
            const resultElement = document.getElementById('costResult');

            // Guard: cost section only renders for verified general users
            if (!baseCostElement || !travelersElement || !daysElement || !resultElement) {
                return;
            }

            // Parse the inner text and inputs safely
            const baseCost = parseFloat(baseCostElement.innerText);
            const travelers = parseInt(travelersElement.value, 10);
            const days = parseInt(daysElement.value, 10);

            // Strict Client-Side Validation (1 to 10 travelers, positive integers only)
            if (isNaN(baseCost) || isNaN(travelers) || isNaN(days) || travelers < 1 || travelers > 10 || days < 1) {
                resultElement.style.color = 'var(--danger)';
                resultElement.innerText = 'Please enter valid travelers (1-10) and positive days.';
                return;
            }

            // Faculty assigned formula: Total = base_cost * travelers * days / 7
            const total = (baseCost * travelers * days) / 7;

            // Output the formatted string safely back to the user interface
            resultElement.style.color = 'var(--primary)';
            resultElement.innerText = 'Estimated total cost: ' + total.toFixed(2) + ' <?php echo e($cost['currency']); ?>';
        }

        // 3. AJAX Comment Section Form Submission handling
        const form = document.getElementById('commentForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const postId = document.getElementById('postId').value;
                const content = document.getElementById('commentContent').value.trim();
                const error = document.getElementById('commentError');

                if (content === '') {
                    error.innerText = 'Comment cannot be empty.';
                    return;
                }
                if (content.length > 500) {
                    error.innerText = 'Comment must be within 500 characters.';
                    return;
                }

                const body = new URLSearchParams();
                body.append('post_id', postId);
                body.append('content', content);

                fetch('index.php?page=ajax&type=add_comment', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: body.toString()
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (!data.success) {
                            error.innerText = data.message;
                            return;
                        }
                        error.innerText = '';
                        document.getElementById('commentContent').value = '';
                        const c = data.comment;
                        const html = `<div class="comment" id="comment-${c.id}">
                <div class="comment-top"><strong>${escapeHtml(c.name)}</strong><small>${escapeHtml(c.created_at)}</small></div>
                <p>${escapeHtml(content)}</p>
                <button class="btn-sm btn-delete" onclick="deleteComment(${c.id})">Delete</button>
            </div>`;
                        document.getElementById('commentList').insertAdjacentHTML('afterbegin', html);
                    });
            });
        }

        // 4. AJAX Comment Deletion handling
        function deleteComment(id) {
            if (!confirm('Delete this comment?')) return;
            const body = new URLSearchParams();
            body.append('comment_id', id);
            fetch('index.php?page=ajax&type=delete_comment', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: body.toString()
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) document.getElementById('comment-' + id).remove();
                    else alert(data.message);
                });
        }
    </script>
</body>

</html>