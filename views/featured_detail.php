<?php
$dest     = $dest     ?? ['title' => '', 'genre' => '', 'country' => '', 'short_history' => '', 'travel_medium_info' => '', 'cost_level' => '', 'base_cost' => 0];
$cost     = $cost     ?? ['base_cost' => 0, 'currency' => 'USD'];
$comments = $comments ?? [];
$key      = intval($_GET['dest'] ?? 0);

function featImage($genre)
{
    $g = strtolower(trim($genre));
    $allowed = ['beach', 'mountain', 'city', 'historical'];
    if (!in_array($g, $allowed, true)) $g = 'default';
    return 'images/' . $g . '.png';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($dest['title']); ?> - Travel Guide</title>
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
            <img class="detail-img" src="<?php echo e(featImage($dest['genre'])); ?>" alt="<?php echo e($dest['genre']); ?> image">
            <div class="detail-content">
                <div class="post-tags">
                    <span><?php echo e($dest['country']); ?></span>
                    <span><?php echo e(ucfirst($dest['genre'])); ?></span>
                    <span><?php echo e(ucfirst($dest['cost_level'])); ?> Cost</span>
                </div>
                <h1><?php echo e($dest['title']); ?></h1>
                <p class="lead-text"><?php echo nl2br(e($dest['short_history'])); ?></p>
            </div>
        </section>

        <section class="info-grid">
            <div class="info-card">
                <h3>Travel Info</h3>
                <p><?php echo nl2br(e($dest['travel_medium_info'])); ?></p>
            </div>
            <div class="info-card">
                <h3>Place Summary</h3>
                <p><strong>Country:</strong> <?php echo e($dest['country']); ?></p>
                <p><strong>Country Representation:</strong> Cultural &amp; Historical Significance of <?php echo e($dest['country']); ?></p>
                <p><strong>Cost Level:</strong> <?php echo e(ucfirst($dest['cost_level'])); ?></p>
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
                <p>Users can add and delete their own comments.</p>
            </div>

            <div class="comment-list">
                <?php if (empty($comments)): ?>
                    <p style="color:var(--muted);">No comments yet. Be the first to comment!</p>
                <?php endif; ?>
                <?php foreach ($comments as $comment): ?>
                    <div class="comment">
                        <div class="comment-top">
                            <strong><?php echo e($comment['name']); ?></strong>
                            <small><?php echo e($comment['created_at']); ?></small>
                        </div>
                        <p><?php echo e($comment['content']); ?></p>
                        <?php if (isVerifiedGeneralUser() && $comment['user_id'] === $_SESSION['user']['id']): ?>
                            <form method="post" action="index.php?page=featured&dest=<?php echo $key; ?>" style="display:inline;">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="comment_id" value="<?php echo e($comment['id']); ?>">
                                <button type="submit" class="btn-sm btn-delete" onclick="return confirm('Delete this comment?')">Delete</button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if (isVerifiedGeneralUser()): ?>
                <form method="post" action="index.php?page=featured&dest=<?php echo $key; ?>" class="form comment-form">
                    <input type="hidden" name="action" value="add">
                    <div class="field">
                        <label>Name</label>
                        <input type="text" value="<?php echo e($_SESSION['user']['name']); ?>" readonly>
                    </div>
                    <div class="field">
                        <label>Comment</label>
                        <textarea name="content" maxlength="500" placeholder="Write your comment"></textarea>
                    </div>
                    <button class="btn btn-primary" type="submit">Post Comment</button>
                </form>
            <?php else: ?>
                <p class="hint">Only verified users can post comments and use cost estimate.</p>
            <?php endif; ?>
        </section>
    </main>

    <footer class="footer">@ Travel Guide. All rights reserved.</footer>

    <script>
        function calculateCost() {
            const baseCostEl  = document.getElementById('baseCost');
            const travelersEl = document.getElementById('travelers');
            const daysEl      = document.getElementById('days');
            const resultEl    = document.getElementById('costResult');

            if (!baseCostEl || !travelersEl || !daysEl || !resultEl) return;

            const baseCost  = parseFloat(baseCostEl.innerText);
            const travelers = parseInt(travelersEl.value, 10);
            const days      = parseInt(daysEl.value, 10);

            if (isNaN(baseCost) || isNaN(travelers) || isNaN(days) || travelers < 1 || travelers > 10 || days < 1) {
                resultEl.style.color = 'var(--danger)';
                resultEl.innerText   = 'Please enter valid travelers (1-10) and positive days.';
                return;
            }

            const total = (baseCost * travelers * days) / 7;
            resultEl.style.color = 'var(--primary)';
            resultEl.innerText   = 'Estimated total cost: ' + total.toFixed(2) + ' <?php echo e($cost['currency']); ?>';
        }
    </script>
</body>

</html>
