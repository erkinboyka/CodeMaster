<?php if (!defined('APP_INIT'))
    die('Direct access not permitted'); ?>
<?php
$posts = $posts ?? [];
$commentsByPost = $commentsByPost ?? [];
$currentUser = $user ?? (function_exists('getCurrentUser') ? getCurrentUser() : null);
$currentUserId = (int) ($currentUser['id'] ?? 0);
$currentUserName = (string) ($currentUser['name'] ?? t('common_user', 'User'));
$currentUserAvatar = (string) ($currentUser['avatar'] ?? 'https://placehold.co/40x40/e5e7eb/6b7280?text=U');

$makeExcerpt = static function (string $text): string {
    $text = trim(preg_replace('/\s+/u', ' ', $text));
    if (mb_strlen($text) > 220) {
        return mb_substr($text, 0, 220) . '...';
    }
    return $text;
};

$guessCategory = static function (string $title, string $content): string {
    $hay = mb_strtolower(trim($title . ' ' . $content), 'UTF-8');
    $rules = [
        'interview' => ['interview', 'собесед', 'интервью', 'мусоҳиба', 'суҳбат', 'technical round', 'phone screen'],
        'contest' => ['contest', 'контест', 'олимпиад', 'hackathon', 'challenge'],
        'compensation' => ['salary', 'compensation', 'pay', 'offer', 'зарплат', 'маош', 'ставка'],
        'feedback' => ['feedback', 'отзыв', 'фидбек', 'review', 'мулоҳиза', 'фикр'],
        'career' => ['career', 'карьер', 'vacancy', 'работ', 'job', 'intern', 'стаж', 'resume', 'cv', 'hr'],
    ];
    foreach ($rules as $cat => $words) {
        foreach ($words as $w) {
            if ($w !== '' && mb_strpos($hay, $w, 0, 'UTF-8') !== false) {
                return $cat;
            }
        }
    }
    return 'for_you';
};
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(function_exists('currentLang') ? currentLang() : 'ru') ?>">

<head>
    <?php include __DIR__ . '/../includes/head_meta.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t('community_page_title', 'Community - CodeMaster') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --bg: #f9fafb;
            --panel: #ffffff;
            --panel-2: #fafafa;
            --line: #e5e7eb;
            --ink: #111827;
            --muted: #6b7280;
            --accent: #6366f1;
            --accent-dark: #4f46e5;
            --chip: #f3f4f6;
            --shadow: 0 18px 45px rgba(15, 23, 42, 0.08);
        }

        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            color: var(--ink);
            background: var(--bg);
            padding-top: 72px;
        }

        h1,
        h2,
        h3 {
            letter-spacing: -0.02em;
        }

        .page {
            max-width: 1180px;
            margin: 0 auto;
            padding: 1.6rem 1rem 3rem;
        }

        .page-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 1.3rem;
        }

        .page-title {
            display: flex;
            flex-direction: column;
            gap: .4rem;
        }

        .page-title h1 {
            margin: 0;
            font-size: 1.8rem;
        }

        .tab-row {
            display: flex;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
            border-bottom: 1px solid var(--line);
        }

        .tab {
            background: transparent;
            border: none;
            padding: .6rem 0;
            font-size: .9rem;
            font-weight: 600;
            color: var(--muted);
            cursor: pointer;
            position: relative;
        }

        .tab.active {
            color: var(--ink);
        }

        .tab.active::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -1px;
            width: 100%;
            height: 2px;
            background: var(--accent);
        }

        .sorts {
            display: flex;
            gap: .8rem;
            align-items: center;
            color: var(--muted);
        }

        .sort {
            background: transparent;
            border: none;
            font-weight: 600;
            color: var(--muted);
            cursor: pointer;
        }

        .sort.active {
            color: var(--ink);
        }

        .sort i {
            margin-right: .35rem;
        }

        .btn {
            border: 1px solid transparent;
            border-radius: 10px;
            padding: .55rem 1rem;
            font-weight: 700;
            font-size: .9rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: .5rem;
        }

        .btn.primary {
            background: var(--accent);
            color: #ffffff;
        }

        .btn.primary:hover {
            background: var(--accent-dark);
        }

        .btn.ghost {
            background: transparent;
            border-color: var(--line);
            color: var(--ink);
        }

        .layout {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 280px;
            gap: 1.4rem;
        }

        .feed {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 16px;
            overflow: hidden;
        }

        .post-row {
            display: grid;
            grid-template-columns: 56px minmax(0, 1fr);
            gap: 1rem;
            padding: 1rem 1.2rem;
            border-bottom: 1px solid var(--line);
            cursor: pointer;
        }

        .post-row:hover {
            background: var(--panel-2);
        }

        .post-row:last-child {
            border-bottom: none;
        }

        .vote-box {
            text-align: center;
            color: var(--muted);
            font-weight: 600;
            font-size: .85rem;
        }

        .vote-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 999px;
            background: var(--chip);
            color: var(--ink);
            margin-bottom: .3rem;
        }

        .post-meta {
            display: flex;
            align-items: center;
            gap: .6rem;
            color: var(--muted);
            font-size: .78rem;
            flex-wrap: wrap;
        }

        .post-meta .meta-name {
            color: #374151;
            font-weight: 600;
        }

        .post-meta img {
            width: 28px;
            height: 28px;
            border-radius: 50%;
        }

        .tag {
            background: var(--chip);
            padding: .15rem .5rem;
            border-radius: 999px;
            font-size: .7rem;
            color: var(--muted);
        }

        .post-title {
            font-size: 1.05rem;
            font-weight: 700;
            margin: .35rem 0 .2rem;
        }

        .post-excerpt {
            color: #4b5563;
            font-size: .92rem;
            line-height: 1.55;
        }

        .post-stats {
            margin-top: .6rem;
            display: flex;
            gap: 1rem;
            color: var(--muted);
            font-size: .8rem;
        }

        .post-stats span {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
        }

        .side-card {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 1rem;
            box-shadow: var(--shadow);
            position: sticky;
            top: 90px;
        }

        .side-card h3 {
            margin: 0 0 .6rem;
            font-size: 1.05rem;
        }

        .side-card p {
            color: var(--muted);
            font-size: .85rem;
            margin: 0 0 1rem;
            line-height: 1.5;
        }

        .modal {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.45);
            display: none;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            z-index: 2000;
        }

        .modal.open {
            display: flex;
        }

        .modal-panel {
            background: var(--panel);
            border-radius: 18px;
            width: min(920px, 100%);
            max-height: 90vh;
            overflow: auto;
            position: relative;
            padding: 1.4rem;
            box-shadow: var(--shadow);
        }

        .modal-close {
            position: absolute;
            top: 14px;
            right: 14px;
            width: 36px;
            height: 36px;
            border-radius: 999px;
            border: 1px solid var(--line);
            background: var(--panel);
            cursor: pointer;
            color: var(--ink);
        }

        .modal-title {
            margin: 0 0 1rem;
            font-size: 1.3rem;
        }

        .field {
            width: 100%;
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: .7rem .8rem;
            font-size: .95rem;
            font-family: inherit;
        }

        .field:focus {
            outline: none;
            border-color: rgba(99, 102, 241, 0.3);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.12);
        }

        .form-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: .8rem;
            margin-top: 1rem;
        }

        .post-detail {
            display: grid;
            grid-template-columns: 60px minmax(0, 1fr);
            gap: 1rem;
        }

        .detail-body h2 {
            margin: .2rem 0 .4rem;
            font-size: 1.4rem;
        }

        .detail-body p {
            color: #374151;
            line-height: 1.6;
            font-size: .95rem;
        }

        .detail-stats {
            display: flex;
            gap: 1rem;
            margin: .8rem 0 1rem;
            color: var(--muted);
            font-size: .85rem;
        }

        .comment-box {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid var(--line);
        }

        .comment-item {
            display: flex;
            gap: .7rem;
            margin-bottom: 1rem;
        }

        .comment-item img {
            width: 34px;
            height: 34px;
            border-radius: 50%;
        }

        .comment-item .meta {
            color: var(--muted);
            font-size: .75rem;
        }

        .comment-item .text {
            color: #374151;
            font-size: .92rem;
        }

        .muted {
            color: var(--muted);
        }

        @media (max-width: 1024px) {
            .layout {
                grid-template-columns: 1fr;
            }

            .side-card {
                position: static;
            }
        }

        @media (max-width: 720px) {
            body {
                padding-top: 64px;
            }

            .post-row {
                grid-template-columns: 44px minmax(0, 1fr);
                padding: .9rem;
            }

            .post-detail {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <?php include 'includes/header.php'; ?>

    <main class="page">
        <div class="page-head">
            <div class="page-title">
                <h1><?= t('community_title', 'Discuss') ?></h1>
                <div class="tab-row" id="topicTabs">
                    <button class="tab active" data-filter="for_you"><?= t('community_tab_for_you', 'For You') ?></button>
                    <button class="tab" data-filter="career"><?= t('community_tab_career', 'Career') ?></button>
                    <button class="tab" data-filter="contest"><?= t('community_tab_contest', 'Contest') ?></button>
                    <button class="tab" data-filter="compensation"><?= t('community_tab_compensation', 'Compensation') ?></button>
                    <button class="tab" data-filter="feedback"><?= t('community_tab_feedback', 'Feedback') ?></button>
                    <button class="tab" data-filter="interview"><?= t('community_tab_interview', 'Interview') ?></button>
                </div>
            </div>
            <div class="sorts" id="sortTabs">
                <button class="sort active" data-sort="votes"><i class="fas fa-arrow-up"></i> <?= t('community_sort_votes', 'Most Votes') ?></button>
                <button class="sort" data-sort="newest"><i class="far fa-clock"></i> <?= t('community_sort_newest', 'Newest') ?></button>
                <button class="btn primary" id="openCreate"><i class="fas fa-plus"></i> <?= t('community_create', 'Create') ?></button>
            </div>
        </div>

        <div class="layout">
            <section class="feed" id="postList">
                <?php if (empty($posts)): ?>
                    <div class="muted" style="padding: 2rem; text-align:center;">
                        <?= t('community_empty', 'No topics yet. Create the first one.') ?>
                    </div>
                <?php else: ?>
                    <?php foreach ($posts as $post): ?>
                        <?php
                        $postId = (int) ($post['id'] ?? 0);
                        $title = (string) ($post['title'] ?? '');
                        $content = (string) ($post['content'] ?? '');
                        $category = $guessCategory($title, $content);
                        $createdAt = (string) ($post['created_at'] ?? '');
                        $createdTs = $createdAt ? (int) strtotime($createdAt) : time();
                        $likes = (int) ($post['likes_count'] ?? 0);
                        $views = (int) ($post['views_count'] ?? 0);
                        $commentsCount = (int) ($post['comments_count'] ?? 0);
                        ?>
                        <article class="post-row" data-post-id="<?= $postId ?>" data-category="<?= htmlspecialchars($category) ?>"
                            data-votes="<?= $likes ?>" data-created="<?= $createdTs ?>"
                            data-author-id="<?= (int) ($post['user_id'] ?? 0) ?>">
                            <div class="vote-box">
                                <div class="vote-icon"><i class="far fa-thumbs-up"></i></div>
                                <div class="post-likes"><?= $likes ?></div>
                            </div>
                            <div>
                                <div class="post-meta">
                                    <img src="<?= htmlspecialchars((string) ($post['author_avatar'] ?? 'https://placehold.co/40x40/e5e7eb/6b7280?text=U')) ?>"
                                        alt="avatar">
                                    <span class="meta-name"><?= htmlspecialchars((string) ($post['author_name'] ?? t('common_user', 'User'))) ?></span>
                                    <span>•</span>
                                    <span><?= htmlspecialchars($createdAt) ?></span>
                                    <span class="tag"><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $category))) ?></span>
                                </div>
                                <div class="post-title"><?= htmlspecialchars($title) ?></div>
                                <div class="post-excerpt"><?= htmlspecialchars($makeExcerpt($content)) ?></div>
                                <div class="post-stats">
                                    <span><i class="far fa-comment"></i><span class="post-comments"><?= $commentsCount ?></span></span>
                                    <span><i class="far fa-eye"></i><span class="post-views"><?= $views ?></span></span>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </section>

            <aside class="side-card">
                <h3><?= t('community_start_title', 'Start a discussion') ?></h3>
                <p><?= t('community_start_desc', 'Share interview experiences, ask about contests, or start a career thread. Keep it focused and helpful.') ?></p>
                <button class="btn primary" id="openCreateSide"><i class="fas fa-pen"></i> <?= t('community_create_post', 'Create Post') ?></button>
            </aside>
        </div>
    </main>

    <div class="modal" id="postModal" aria-hidden="true">
        <div class="modal-panel">
            <button class="modal-close" data-close aria-label="Close"><i class="fas fa-xmark"></i></button>
            <div id="postModalBody"></div>
        </div>
    </div>

    <div class="modal" id="editorModal" aria-hidden="true">
        <div class="modal-panel">
            <button class="modal-close" data-close aria-label="Close"><i class="fas fa-xmark"></i></button>
            <h3 class="modal-title" id="editorTitle"><?= t('community_modal_create', 'Create Post') ?></h3>
            <div>
                <input id="editorPostTitle" type="text" maxlength="255" class="field" placeholder="<?= t('community_title_input', 'Title') ?>">
                <textarea id="editorPostContent" maxlength="10000" class="field" rows="6" placeholder="<?= t('community_content_input', 'Write your post') ?>"
                    style="margin-top:.8rem;"></textarea>
                <div class="form-actions">
                    <div class="muted" id="editorHint"><?= t('community_hint', 'Be clear and respectful.') ?></div>
                    <button id="savePostBtn" class="btn primary"><i class="fas fa-paper-plane"></i> <?= t('community_publish', 'Publish') ?></button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="confirmModal" aria-hidden="true">
        <div class="modal-panel" style="width:min(480px,100%);">
            <button class="modal-close" data-close aria-label="Close"><i class="fas fa-xmark"></i></button>
            <h3 class="modal-title"><?= t('community_delete_title', 'Delete post?') ?></h3>
            <p class="muted"><?= t('community_delete_desc', 'This will permanently remove the post and all its comments.') ?></p>
            <div class="form-actions">
                <button class="btn ghost" data-close><?= t('common_cancel', 'Cancel') ?></button>
                <button class="btn primary" id="confirmDeleteBtn"><i class="fas fa-trash"></i> <?= t('community_delete', 'Delete') ?></button>
            </div>
        </div>
    </div>

    <script>
        const communityServerError = '<?= htmlspecialchars(t('common_server_error', 'Server error')) ?>';
        const communityI18n = {
            createPost: '<?= htmlspecialchars(t('community_modal_create', 'Create Post')) ?>',
            editPost: '<?= htmlspecialchars(t('community_edit_post', 'Edit Post')) ?>',
            publish: '<?= htmlspecialchars(t('community_publish', 'Publish')) ?>',
            save: '<?= htmlspecialchars(t('common_save', 'Save')) ?>',
            loading: '<?= htmlspecialchars(t('common_loading', 'Loading...')) ?>',
            like: '<?= htmlspecialchars(t('community_like', 'Like')) ?>',
            liked: '<?= htmlspecialchars(t('community_liked', 'Liked')) ?>',
            edit: '<?= htmlspecialchars(t('common_edit', 'Edit')) ?>',
            del: '<?= htmlspecialchars(t('community_delete', 'Delete')) ?>',
            comments: '<?= htmlspecialchars(t('community_comments', 'Comments')) ?>',
            noComments: '<?= htmlspecialchars(t('community_no_comments', 'No comments yet.')) ?>',
            writeComment: '<?= htmlspecialchars(t('community_comment_placeholder', 'Write a comment')) ?>',
            comment: '<?= htmlspecialchars(t('community_comment', 'Comment')) ?>',
            user: '<?= htmlspecialchars(t('common_user', 'User')) ?>'
        };
        const postList = document.getElementById('postList');
        const tabs = Array.from(document.querySelectorAll('#topicTabs .tab'));
        const sorts = Array.from(document.querySelectorAll('#sortTabs .sort'));
        const openCreateBtn = document.getElementById('openCreate');
        const openCreateSide = document.getElementById('openCreateSide');
        const postModal = document.getElementById('postModal');
        const editorModal = document.getElementById('editorModal');
        const confirmModal = document.getElementById('confirmModal');
        const postModalBody = document.getElementById('postModalBody');
        const editorTitle = document.getElementById('editorTitle');
        const editorPostTitle = document.getElementById('editorPostTitle');
        const editorPostContent = document.getElementById('editorPostContent');
        const savePostBtn = document.getElementById('savePostBtn');
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
        const currentUserId = <?= (int) $currentUserId ?>;
        const currentUserName = '<?= htmlspecialchars($currentUserName) ?>';
        const currentUserAvatar = '<?= htmlspecialchars($currentUserAvatar) ?>';
        let editorMode = 'create';
        let editorPostId = null;
        let deletePostId = null;

        function postJson(action, payload) {
            return fetch(`?action=${action}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify(payload || {})
            }).then((r) => r.json());
        }

        function openModal(modal) {
            if (!modal)
                return;
            modal.classList.add('open');
            modal.setAttribute('aria-hidden', 'false');
        }

        function closeModal(modal) {
            if (!modal)
                return;
            modal.classList.remove('open');
            modal.setAttribute('aria-hidden', 'true');
        }

        document.querySelectorAll('[data-close]').forEach(btn => {
            btn.addEventListener('click', () => {
                closeModal(btn.closest('.modal'));
            });
        });

        function applyFilterAndSort() {
            if (!postList)
                return;
            const activeTab = tabs.find(t => t.classList.contains('active'))?.dataset.filter || 'for_you';
            const activeSort = sorts.find(s => s.classList.contains('active'))?.dataset.sort || 'votes';
            const items = Array.from(postList.querySelectorAll('.post-row'));
            items.forEach(item => {
                const cat = item.dataset.category || 'for_you';
                item.style.display = (activeTab === 'for_you' || cat === activeTab) ? '' : 'none';
            });
            const visible = items.filter(item => item.style.display !== 'none');
            visible.sort((a, b) => {
                if (activeSort === 'newest') {
                    return Number(b.dataset.created || 0) - Number(a.dataset.created || 0);
                }
                return Number(b.dataset.votes || 0) - Number(a.dataset.votes || 0);
            });
            visible.forEach(item => postList.appendChild(item));
        }

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                tabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
                applyFilterAndSort();
            });
        });

        sorts.forEach(sort => {
            sort.addEventListener('click', () => {
                sorts.forEach(s => s.classList.remove('active'));
                sort.classList.add('active');
                applyFilterAndSort();
            });
        });

        applyFilterAndSort();

        function openEditor(mode, post) {
            editorMode = mode;
            editorPostId = post ? Number(post.id || 0) : null;
            editorTitle.textContent = mode === 'edit' ? communityI18n.editPost : communityI18n.createPost;
            editorPostTitle.value = post ? (post.title || '') : '';
            editorPostContent.value = post ? (post.content || '') : '';
            savePostBtn.innerHTML = mode === 'edit'
                ? `<i class="fas fa-save"></i> ${communityI18n.save}`
                : `<i class="fas fa-paper-plane"></i> ${communityI18n.publish}`;
            openModal(editorModal);
            editorPostTitle.focus();
        }

        function updateRowCounts(postId, data) {
            const row = postList?.querySelector(`.post-row[data-post-id="${postId}"]`);
            if (!row)
                return;
            if (typeof data.likes === 'number') {
                const likesEl = row.querySelector('.post-likes');
                if (likesEl)
                    likesEl.textContent = String(data.likes);
                row.dataset.votes = String(data.likes);
            }
            if (typeof data.views === 'number') {
                const viewsEl = row.querySelector('.post-views');
                if (viewsEl)
                    viewsEl.textContent = String(data.views);
            }
            if (typeof data.comments === 'number') {
                const commentsEl = row.querySelector('.post-comments');
                if (commentsEl)
                    commentsEl.textContent = String(data.comments);
            }
        }

        function escapeHtml(value) {
            return String(value || '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/\"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function renderComment(comment) {
            const avatar = comment.author_avatar || 'https://placehold.co/40x40/e5e7eb/6b7280?text=U';
            const name = escapeHtml(comment.author_name || communityI18n.user);
            const created = escapeHtml(comment.created_at || '');
            const content = escapeHtml(comment.content || '');
            return `
                <div class="comment-item">
                    <img src="${avatar}" alt="avatar">
                    <div>
                        <div class="meta">${name} • ${created}</div>
                        <div class="text">${content}</div>
                    </div>
                </div>
            `;
        }

        function openPost(postId) {
            if (!postId)
                return;
            openModal(postModal);
            if (postModalBody) {
                postModalBody.innerHTML = `<div class="muted">${communityI18n.loading}</div>`;
            }
            postJson('community-view-post', { post_id: postId })
                .then((data) => {
                    if (!data || !data.success || !data.post) {
                        throw new Error((data && data.message) ? data.message : communityServerError);
                    }
                    const post = data.post;
                    const comments = Array.isArray(data.comments) ? data.comments : [];
                    const isOwner = Number(post.user_id || 0) === currentUserId;
                    const likes = Number(post.likes_count || 0);
                    const views = Number(post.views_count || 0);
                    const likedByMe = Number(post.liked_by_me || 0) > 0;
                    let commentsCount = Number(post.comments_count || comments.length || 0);
                    updateRowCounts(postId, { likes, views, comments: commentsCount });

                    const safeTitle = escapeHtml(post.title || '');
                    const safeContent = escapeHtml(post.content || '').replace(/\n/g, '<br>');
                    const safeAuthor = escapeHtml(post.author_name || communityI18n.user);
                    const safeCreated = escapeHtml(post.created_at || '');
                    postModalBody.innerHTML = `
                        <div class="post-detail">
                            <div class="vote-box">
                                <div class="vote-icon"><i class="far fa-thumbs-up"></i></div>
                                <div id="detailLikes">${likes}</div>
                            </div>
                            <div class="detail-body">
                                <div class="post-meta">
                                    <img src="${post.author_avatar || 'https://placehold.co/40x40/e5e7eb/6b7280?text=U'}" alt="avatar">
                                    <span>${safeAuthor}</span>
                                    <span>•</span>
                                    <span>${safeCreated}</span>
                                </div>
                                <h2>${safeTitle}</h2>
                                <p>${safeContent}</p>
                                <div class="detail-stats">
                                    <span><i class="far fa-comment"></i> <span id="detailComments">${commentsCount}</span></span>
                                    <span><i class="far fa-eye"></i> <span id="detailViews">${views}</span></span>
                                </div>
                                <div style="display:flex; gap:.6rem; flex-wrap:wrap;">
                                    <button class="btn ghost" id="likePostBtn" ${likedByMe ? 'disabled' : ''}><i class="far fa-thumbs-up"></i> ${likedByMe ? communityI18n.liked : communityI18n.like}</button>
                                    ${isOwner ? `<button class="btn ghost" id="editPostBtn"><i class="fas fa-pen"></i> ${communityI18n.edit}</button>` : ''}
                                    ${isOwner ? `<button class="btn ghost" id="deletePostBtn"><i class="fas fa-trash"></i> ${communityI18n.del}</button>` : ''}
                                </div>
                                <div class="comment-box">
                                    <h3 class="modal-title" style="font-size:1.05rem;">${communityI18n.comments}</h3>
                                    <div id="commentList">
                                        ${comments.length ? comments.map(renderComment).join('') : `<div class="muted">${communityI18n.noComments}</div>`}
                                    </div>
                                    <div style="margin-top:1rem;">
                                        <textarea id="newComment" class="field" rows="3" placeholder="${communityI18n.writeComment}"></textarea>
                                        <div class="form-actions">
                                            <div></div>
                                            <button class="btn primary" id="sendCommentBtn"><i class="fas fa-paper-plane"></i> ${communityI18n.comment}</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;

                    const likeBtn = document.getElementById('likePostBtn');
                    likeBtn?.addEventListener('click', () => {
                        if (likeBtn.disabled)
                            return;
                        likeBtn.disabled = true;
                        postJson('community-like-post', { post_id: postId })
                            .then((resp) => {
                                if (!resp || !resp.success)
                                    throw new Error((resp && resp.message) ? resp.message : communityServerError);
                                const likesCount = Number(resp.likes || 0);
                                const detailLikes = document.getElementById('detailLikes');
                                if (detailLikes)
                                    detailLikes.textContent = String(likesCount);
                                updateRowCounts(postId, { likes: likesCount });
                                if (resp.already_liked || resp.liked) {
                                    likeBtn.innerHTML = `<i class="far fa-thumbs-up"></i> ${communityI18n.liked}`;
                                }
                            })
                            .catch((e) => alert(e && e.message ? e.message : communityServerError))
                            .finally(() => {
                                likeBtn.disabled = likeBtn.textContent.includes(communityI18n.liked);
                            });
                    });

                    const editBtn = document.getElementById('editPostBtn');
                    editBtn?.addEventListener('click', () => {
                        openEditor('edit', post);
                    });

                    const deleteBtn = document.getElementById('deletePostBtn');
                    deleteBtn?.addEventListener('click', () => {
                        deletePostId = postId;
                        openModal(confirmModal);
                    });

                    const sendCommentBtn = document.getElementById('sendCommentBtn');
                    const newComment = document.getElementById('newComment');
                    sendCommentBtn?.addEventListener('click', () => {
                        const content = (newComment?.value || '').trim();
                        if (!content)
                            return;
                        sendCommentBtn.disabled = true;
                        postJson('community-create-comment', { post_id: postId, content })
                            .then((resp) => {
                                if (!resp || !resp.success)
                                    throw new Error((resp && resp.message) ? resp.message : communityServerError);
                                const commentList = document.getElementById('commentList');
                                const commentHtml = renderComment({
                                    author_name: currentUserName || 'You',
                                    author_avatar: currentUserAvatar || 'https://placehold.co/40x40/e5e7eb/6b7280?text=U',
                                    created_at: new Date().toLocaleString(),
                                    content
                                });
                                if (commentList) {
                                    if (commentList.querySelector('.muted')) {
                                        commentList.innerHTML = '';
                                    }
                                    commentList.insertAdjacentHTML('beforeend', commentHtml);
                                }
                                if (newComment)
                                    newComment.value = '';
                                commentsCount += 1;
                                const newCount = commentsCount;
                                const detailComments = document.getElementById('detailComments');
                                if (detailComments)
                                    detailComments.textContent = String(newCount);
                                updateRowCounts(postId, { comments: newCount });
                            })
                            .catch((e) => alert(e && e.message ? e.message : communityServerError))
                            .finally(() => {
                                sendCommentBtn.disabled = false;
                            });
                    });
                })
                .catch((e) => {
                    if (postModalBody)
                        postModalBody.innerHTML = `<div class="muted">${e && e.message ? e.message : communityServerError}</div>`;
                });
        }

        postList?.addEventListener('click', (e) => {
            const row = e.target.closest('.post-row');
            if (!row)
                return;
            openPost(Number(row.dataset.postId || 0));
        });

        openCreateBtn?.addEventListener('click', () => openEditor('create'));
        openCreateSide?.addEventListener('click', () => openEditor('create'));

        savePostBtn?.addEventListener('click', () => {
            const title = (editorPostTitle?.value || '').trim();
            const content = (editorPostContent?.value || '').trim();
            if (!title || !content) {
                alert('<?= htmlspecialchars(t('community_fill_required', 'Fill in title and content')) ?>');
                return;
            }
            savePostBtn.disabled = true;
            const action = editorMode === 'edit' ? 'community-update-post' : 'community-create-post';
            const payload = editorMode === 'edit'
                ? { post_id: editorPostId, title, content }
                : { title, content };
            postJson(action, payload)
                .then((data) => {
                    if (!data || !data.success)
                        throw new Error((data && data.message) ? data.message : communityServerError);
                    window.location.reload();
                })
                .catch((e) => alert(e && e.message ? e.message : communityServerError))
                .finally(() => {
                    savePostBtn.disabled = false;
                });
        });

        confirmDeleteBtn?.addEventListener('click', () => {
            if (!deletePostId)
                return;
            confirmDeleteBtn.disabled = true;
            postJson('community-delete-post', { post_id: deletePostId })
                .then((data) => {
                    if (!data || !data.success)
                        throw new Error((data && data.message) ? data.message : communityServerError);
                    window.location.reload();
                })
                .catch((e) => alert(e && e.message ? e.message : communityServerError))
                .finally(() => {
                    confirmDeleteBtn.disabled = false;
                });
        });

        window.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                [postModal, editorModal, confirmModal].forEach(closeModal);
            }
        });
    </script>
</body>

</html>
