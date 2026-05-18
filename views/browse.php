<?php
$countries = $countries ?? [];
$posts = $posts ?? [];
$genres = $genres ?? [];

function postImage($genre)
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
    <title>Browse Posts - Travel Guide</title>
    <link rel="stylesheet" href="style.css">
</head>

<body class="app-body">
    <nav class="navbar">
        <div class="navbar-inner">
            <a class="brand" href="index.php?page=browse"><span class="brand-icon">✈️</span> Travel Guide</a>
            <div class="nav-user">
                <?php if (isLoggedIn()): ?>
                    <div class="user-pill">
                        <span class="user-avatar"><?php echo e(substr($_SESSION['user']['name'], 0, 1)); ?></span>
                        <span class="user-meta"><span class="user-name"><?php echo e($_SESSION['user']['name']); ?></span><span class="user-role"><?php echo e($_SESSION['user']['role']); ?></span></span>
                    </div>
                    <a class="btn-logout" href="index.php?page=logout">Logout</a>
                <?php else: ?>
                    <a class="btn btn-primary" href="index.php?page=login">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <header class="hero">
        <div class="hero-content">
            <h1>Find your next travel destination</h1>
            <p>Browse approved places, search with AJAX filters, read details, comment, and estimate probable tour cost.</p>
            <div class="hero-actions">
                <a class="btn btn-primary" href="#posts">Explore Posts</a>
                <?php if (!isLoggedIn()): ?><a class="btn btn-light" href="index.php?page=login">Login as Demo User</a><?php endif; ?>
            </div>
        </div>
    </header>

    <main class="main-content" id="posts">
        <section class="toolbar-card">
            <div class="section-heading">
                <h2>Search & Filter</h2>
                <p>Results update instantly without reloading the page.</p>
            </div>
            <div class="filter-grid">
                <div class="field search-field">
                    <label>Live Search</label>
                    <input type="text" id="searchBox" placeholder="Search by title or country">
                </div>

                <div class="field">
                    <label>Country</label>
                    <select id="countryFilter">
                        <option value="">All Countries</option>
                        <option value="India">India</option>
                        <option value="Nepal">Nepal</option>
                        <?php foreach ($countries as $c): ?>
                            <?php if (!in_array($c['country'], ['India', 'Nepal'], true)): ?>
                                <option value="<?php echo e($c['country']); ?>"><?php echo e($c['country']); ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="field">
                    <label>Genres (multi-select)</label>
                    <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-top: 5px;">
                        <?php foreach ($genres as $g): ?>
                            <label><input type="checkbox" name="genreFilter" value="<?php echo e($g['genre']); ?>"> <?php echo e(ucfirst($g['genre'])); ?></label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="field">
                    <label>Cost Level</label>
                    <div style="display: flex; gap: 10px; margin-top: 5px;">
                        <label><input type="radio" name="costFilter" value="" checked> All</label>
                        <label><input type="radio" name="costFilter" value="low"> Low</label>
                        <label><input type="radio" name="costFilter" value="medium"> Medium</label>
                        <label><input type="radio" name="costFilter" value="high"> High</label>
                    </div>
                </div>
            </div>
        </section>

        <div class="count-line"><span id="resultCount"><?php echo count($posts); ?></span> approved post(s) found</div>

        <section id="postGrid" class="post-grid">
            <?php foreach ($posts as $post): ?>
                <article class="post-card">
                    <img class="post-img" src="<?php echo e(postImage($post['genre'])); ?>" alt="<?php echo e($post['genre']); ?> place image">
                    <div class="post-body">
                        <div class="post-tags">
                            <span><?php echo e($post['country']); ?></span>
                            <span><?php echo e(ucfirst($post['genre'])); ?></span>
                            <span><?php echo e(ucfirst($post['cost_level'])); ?></span>
                        </div>
                        <h3><?php echo e($post['title']); ?></h3>
                        <p><?php echo e(substr($post['short_history'], 0, 130)); ?>...</p>
                        <a class="read-link" href="index.php?page=detail&id=<?php echo intval($post['id']); ?>">Read More →</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </section>
    </main>

    <section class="featured-section">
        <div class="main-content">
            <div class="section-heading" style="margin-bottom:24px;">
                <h2>Featured Destinations</h2>
                <p>Hand-picked top places across India, Sri Lanka, Nepal, and France.</p>
            </div>

            <?php
            $featuredDests = $featuredDests ?? [];
            $regionFlags   = ['India' => '🇮🇳', 'Sri Lanka' => '🇱🇰', 'Nepal' => '🇳🇵', 'France' => '🇫🇷'];
            $regions       = [];
            foreach ($featuredDests as $key => $d) {
                $regions[$d['country']][] = ['key' => $key, 'dest' => $d];
            }
            foreach ($regions as $country => $entries):
                $flag  = $regionFlags[$country] ?? '';
                $n     = count($entries);
            ?>
                <div class="region-group">
                    <h3 class="region-title"><?php echo $flag; ?> <?php echo e($country); ?> — Top <?php echo $n; ?> Place<?php echo $n !== 1 ? 's' : ''; ?></h3>
                    <div class="post-grid featured-grid">
                        <?php foreach ($entries as $entry):
                            $d    = $entry['dest'];
                            $key  = $entry['key'];
                            $genre = strtolower(trim($d['genre']));
                            $imgSrc = in_array($genre, ['beach', 'mountain', 'city', 'historical'], true) ? $genre : 'default';
                        ?>
                            <article class="post-card"
                                data-country="<?php echo e($d['country']); ?>"
                                data-genre="<?php echo e($genre); ?>"
                                data-cost="<?php echo e($d['cost_level']); ?>"
                                data-title="<?php echo e($d['title']); ?>">
                                <img class="post-img" src="images/<?php echo $imgSrc; ?>.svg" alt="<?php echo e($genre); ?> place image">
                                <div class="post-body">
                                    <div class="post-tags">
                                        <span><?php echo e($d['country']); ?></span>
                                        <span><?php echo e(ucfirst($genre)); ?></span>
                                        <span><?php echo e(ucfirst($d['cost_level'])); ?></span>
                                        <span class="tag-approved">Approved</span>
                                    </div>
                                    <h3><?php echo e($d['title']); ?></h3>
                                    <p><?php echo e(substr($d['short_history'], 0, 130)); ?>...</p>
                                    <a class="read-link" href="index.php?page=featured&dest=<?php echo $d['id']; ?>">Read More →</a>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>

        </div>
    </section>

    <footer class="footer">@ Travel Guide. All rights reserved.</footer>

    <script>
        const postGrid = document.getElementById('postGrid');
        const resultCount = document.getElementById('resultCount');
        const searchBox = document.getElementById('searchBox');
        const countryFilter = document.getElementById('countryFilter');

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text ?? '';
            return div.innerHTML;
        }

        function imageForGenre(genre) {
            const g = (genre || '').toLowerCase();
            const allowed = ['beach', 'mountain', 'city', 'historical'];
            return 'images/' + (allowed.includes(g) ? g : 'default') + '.svg';
        }

        function renderPosts(posts) {
            resultCount.innerText = posts.length;
            if (!posts.length) {
                postGrid.innerHTML = '<div class="empty-state">No approved posts found. Try another keyword or filter.</div>';
                return;
            }
            postGrid.innerHTML = posts.map(post => `
        <article class="post-card">
            <img class="post-img" src="${imageForGenre(post.genre)}" alt="${escapeHtml(post.genre)} place image">
            <div class="post-body">
                <div class="post-tags">
                    <span>${escapeHtml(post.country)}</span>
                    <span>${escapeHtml(post.genre)}</span>
                    <span>${escapeHtml(post.cost_level)}</span>
                </div>
                <h3>${escapeHtml(post.title)}</h3>
                <p>${escapeHtml((post.short_history || '').substring(0,130))}...</p>
                <a class="read-link" href="index.php?page=detail&id=${post.id}">Read More →</a>
            </div>
        </article>
    `).join('');
        }

        function filterFeatured(q, country, genres, cost) {
            document.querySelectorAll('.region-group').forEach(group => {
                let anyVisible = false;
                group.querySelectorAll('.post-card').forEach(card => {
                    const cardCountry = (card.dataset.country || '').toLowerCase();
                    const cardGenre = (card.dataset.genre || '').toLowerCase();
                    const cardCost = (card.dataset.cost || '').toLowerCase();
                    const cardTitle = (card.dataset.title || '').toLowerCase();

                    const matchQ = !q || cardTitle.includes(q.toLowerCase()) || cardCountry.includes(q.toLowerCase());
                    const matchCountry = !country || cardCountry === country.toLowerCase();
                    const matchGenre = !genres.length || genres.includes(cardGenre);
                    const matchCost = !cost || cardCost === cost.toLowerCase();

                    const visible = matchQ && matchCountry && matchGenre && matchCost;
                    card.style.display = visible ? '' : 'none';
                    if (visible) anyVisible = true;
                });
                group.style.display = anyVisible ? '' : 'none';
            });
        }

        searchBox.addEventListener('keyup', function() {
            const q = this.value.trim();
            fetch('index.php?page=ajax&type=search&q=' + encodeURIComponent(q))
                .then(res => res.json())
                .then(data => {
                    renderPosts(data.posts || []);
                    filterFeatured(q, '', [], '');
                });
        });

        function applyFilters() {
            searchBox.value = '';
            const checkedGenres = Array.from(document.querySelectorAll('input[name="genreFilter"]:checked')).map(el => el.value);
            const selectedCost = document.querySelector('input[name="costFilter"]:checked')?.value || '';
            const selectedCountry = countryFilter.value;

            const url = 'index.php?page=ajax&type=filter' +
                '&country=' + encodeURIComponent(selectedCountry) +
                '&genre=' + encodeURIComponent(checkedGenres.join(',')) +
                '&cost=' + encodeURIComponent(selectedCost);
            fetch(url).then(res => res.json()).then(data => {
                renderPosts(data.posts || []);
                filterFeatured('', selectedCountry, checkedGenres, selectedCost);
            });
        }

        countryFilter.addEventListener('change', applyFilters);
        document.querySelectorAll('input[name="genreFilter"]').forEach(el => el.addEventListener('change', applyFilters));
        document.querySelectorAll('input[name="costFilter"]').forEach(el => el.addEventListener('change', applyFilters));

        // ===== Featured Destination Modal =====
        const genreImgMap = {
            beach: 'beach',
            mountain: 'mountain',
            city: 'city',
            historical: 'historical'
        };

        document.querySelectorAll('.feat-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                openDestModal(this.closest('.feat-card'));
            });
        });

        function openDestModal(card) {
            const d = card.dataset;
            document.getElementById('modal-title').textContent = d.title;
            document.getElementById('modal-country').textContent = d.country;
            document.getElementById('modal-genre').textContent = d.genre;
            document.getElementById('modal-cost-label').textContent = d.cost + ' Cost';
            document.getElementById('modal-desc').textContent = d.desc;
            document.getElementById('modal-travel').textContent = d.travel;
            document.getElementById('modal-base-cost').textContent = d.base;

            const g = (d.genre || '').toLowerCase();
            const allowed = ['beach', 'mountain', 'city', 'historical'];
            document.getElementById('modal-img').src = 'public/images/' + (allowed.includes(g) ? g : 'default') + '.svg';
            document.getElementById('modal-img').alt = d.genre + ' image';

            document.getElementById('modal-travelers').value = 1;
            document.getElementById('modal-days').value = 7;
            document.getElementById('modal-result').textContent = '';

            currentModalDest = d.title;
            renderModalComments();
            const commentContent = document.getElementById('modal-comment-content');
            if (commentContent) commentContent.value = '';
            const commentError = document.getElementById('modal-comment-error');
            if (commentError) commentError.textContent = '';

            document.getElementById('destModal').classList.add('open');
            document.body.style.overflow = 'hidden';
        }

        function closeDestModal() {
            document.getElementById('destModal').classList.remove('open');
            document.body.style.overflow = '';
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeDestModal();
        });

        function calcDestCost() {
            const base = parseFloat(document.getElementById('modal-base-cost').textContent);
            const travelers = parseInt(document.getElementById('modal-travelers').value, 10);
            const days = parseInt(document.getElementById('modal-days').value, 10);
            const result = document.getElementById('modal-result');

            if (isNaN(travelers) || isNaN(days) || travelers < 1 || travelers > 10 || days < 1) {
                result.style.color = 'var(--danger)';
                result.textContent = 'Please enter valid travelers (1–10) and positive days.';
                return;
            }

            const total = (base * travelers * days) / 7;
            result.style.color = 'var(--primary)';
            result.textContent = 'Estimated total cost: ' + total.toFixed(2) + ' USD';
        }

        const modalCurrentUser = <?php echo isVerifiedGeneralUser() ? json_encode($_SESSION['user']['name']) : 'null'; ?>;

        const modalComments = {};
        let currentModalDest = '';

        function renderModalComments() {
            const list = document.getElementById('modal-comment-list');
            const items = modalComments[currentModalDest] || [];
            if (!items.length) {
                list.innerHTML = '<p style="color:var(--muted);margin-bottom:8px;">No comments yet. Be the first to comment!</p>';
                return;
            }
            list.innerHTML = items.map(c => `
                <div class="comment" id="mc-${c.id}">
                    <div class="comment-top">
                        <strong>${escapeHtml(c.name)}</strong>
                        <small>${escapeHtml(c.time)}</small>
                    </div>
                    <p>${escapeHtml(c.content)}</p>
                    ${c.mine ? `<button class="btn-sm btn-delete" onclick="deleteModalComment(${c.id})">Delete</button>` : ''}
                </div>
            `).join('');
        }

        function deleteModalComment(id) {
            if (!confirm('Delete this comment?')) return;
            if (modalComments[currentModalDest]) {
                modalComments[currentModalDest] = modalComments[currentModalDest].filter(c => c.id !== id);
                renderModalComments();
            }
        }

        const modalCommentForm = document.getElementById('modal-comment-form');
        if (modalCommentForm) {
            modalCommentForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const content = document.getElementById('modal-comment-content').value.trim();
                const error = document.getElementById('modal-comment-error');

                if (content === '') {
                    error.textContent = 'Comment cannot be empty.';
                    return;
                }
                if (content.length > 500) {
                    error.textContent = 'Comment must be within 500 characters.';
                    return;
                }

                error.textContent = '';
                if (!modalComments[currentModalDest]) modalComments[currentModalDest] = [];
                modalComments[currentModalDest].unshift({
                    id: ++modalCommentSeq,
                    name: modalCurrentUser,
                    content: content,
                    time: new Date().toLocaleString(),
                    mine: true
                });
                document.getElementById('modal-comment-content').value = '';
                renderModalComments();
            });
        }
    </script>
</body>

</html>