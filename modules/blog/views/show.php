<?php $id = isset($post['id']) ? (int)$post['id'] : 0; ?>
<?php

use Modules\Blog\Models\Post;

$isAdmin = \Core\Auth::check();
$recent = array_slice(Post::all(), 0, 5);
$currentUri = $_SERVER['REQUEST_URI'] ?? '/';
?>
<style>
    /* Basic blog styling + layout (top menu, sidebar, toolbar) */
    .blog-container {
        max-width: 800px;
        margin: 20px auto;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial;
    }

    #post-title {
        font-size: 28px;
        margin-bottom: 10px;
        border-bottom: 1px solid #eee;
        padding-bottom: 6px;
    }

    #post-title[contenteditable="true"] {
        outline: none;
    }

    #post-content {
        min-height: 160px;
        padding: 12px;
        background: #fff;
        border: 1px solid #eee;
        border-radius: 4px;
    }

    #post-content[contenteditable="true"] {
        outline: none;
    }

    #save-post {
        background: #0078d4;
        color: #fff;
        border: none;
        padding: 8px 12px;
        border-radius: 6px;
        cursor: pointer;
    }

    #save-status {
        margin-left: 12px;
        color: #666;
    }

    /* Toolbar */
    #editor-toolbar {
        margin-bottom: 10px;
        display: flex;
        gap: 8px;
        align-items: center
    }

    #editor-toolbar button {
        padding: 6px 8px;
        border: 1px solid #e3e3e3;
        background: #fafafa;
        border-radius: 4px;
        cursor: pointer
    }

    #editor-toolbar button:hover {
        background: #fff
    }

    /* Top menu and layout */
    .top-menu {
        background: linear-gradient(180deg, #ffffff, #fbfbfb);
        border-bottom: 1px solid #e6e6e6;
        padding: 10px 20px;
        display: flex;
        align-items: center
    }

    .top-menu a {
        margin-right: 18px;
        color: #333;
        text-decoration: none;
        font-weight: 500
    }

    .top-menu a:hover {
        color: #0078d4
    }

    .page-wrap {
        display: flex;
        gap: 28px;
        max-width: 1100px;
        margin: 20px auto;
        padding: 0 16px;
        align-items: flex-start
    }

    .main {
        flex: 1
    }

    .sidebar {
        width: 280px;
        background: #fff;
        border: 1px solid #f0f0f0;
        border-radius: 6px;
        padding: 12px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02)
    }

    .recent-post {
        padding: 8px 0
    }

    @media (max-width:800px) {
        .page-wrap {
            flex-direction: column;
            padding: 0 12px
        }

        .sidebar {
            width: 100%;
            order: 2
        }
    }
</style>

<?php $isAdmin = \Core\Auth::check();
$currentUri = $_SERVER['REQUEST_URI'] ?? '/';
?>
<header class="top-menu">
    <a href="/">Home</a>
    <a href="/blog">Blog</a>
    <?php if ($isAdmin): ?>
        <a href="/admin/users">Admin</a>
        <a href="/admin/logout">Logout</a>
    <?php else: ?>
        <a href="/admin/login?redirect=<?= urlencode($currentUri) ?>">Admin Login</a>
    <?php endif; ?>
</header>

<div class="page-wrap">
    <main class="main">
        <div class="blog-container">
            <?php if ($isAdmin): ?>
                <h1 id="post-title" contenteditable="true"><?= htmlspecialchars($post['title']) ?></h1>
                <div id="post-content" contenteditable="true"><?= $post['content'] ?></div>

                <p>
                    <button id="save-post">Save</button>
                    <span id="save-status"></span>
                </p>
            <?php else: ?>
                <h1><?= htmlspecialchars($post['title']) ?></h1>
                <div><?= $post['content'] ?></div>
                <p>
                    <a href="/admin/login?redirect=<?= urlencode($currentUri) ?>">Login to edit this post</a>
                </p>
            <?php endif; ?>
        </div>
    </main>

    <aside class="sidebar">
        <h3>Recent Posts</h3>
        <?php foreach ($recent as $r): ?>
            <div class="recent-post"><a href="/blog/<?= $r['id'] ?>"><?= htmlspecialchars($r['title']) ?></a></div>
        <?php endforeach; ?>
    </aside>
</div>

<script>
    (function() {
        var isAdmin = <?= $isAdmin ? 'true' : 'false' ?>;
        if (!isAdmin) return;

        // Simple toolbar: Bold, Italic, UL, OL, Link, Toggle HTML
        var toolbar = document.createElement('div');
        toolbar.id = 'editor-toolbar';
        toolbar.innerHTML = '<button type="button" data-cmd="bold"><strong>B</strong></button>' +
            '<button type="button" data-cmd="italic"><em>I</em></button>' +
            '<button type="button" data-cmd="insertUnorderedList">• List</button>' +
            '<button type="button" data-cmd="insertOrderedList">1. List</button>' +
            '<button type="button" data-cmd="createLink">Link</button>' +
            '<button type="button" id="toggle-html">HTML</button>';

        toolbar.style.marginBottom = '8px';
        toolbar.style.gap = '6px';
        toolbar.style.display = 'flex';

        var container = document.querySelector('.blog-container');
        if (container && container.firstChild) container.prepend(toolbar);

        function doCommand(cmd) {
            if (cmd === 'createLink') {
                var url = prompt('Enter link URL (must start with http(s) or /):', 'https://');
                if (!url) return;
                url = url.trim();
                if (!/^https?:\/\//i.test(url) && url.charAt(0) !== '/') {
                    alert('Only http(s) or root-relative links are allowed');
                    return;
                }
                document.execCommand('createLink', false, url);
                return;
            }
            document.execCommand(cmd, false, null);
        }

        toolbar.addEventListener('click', function(e) {
            var btn = e.target.closest('button');
            if (!btn) return;
            var cmd = btn.getAttribute('data-cmd');
            if (cmd) {
                doCommand(cmd);
            }
        });

        // Toggle HTML view
        var toggleBtn = document.getElementById('toggle-html');
        var editorArea = document.getElementById('post-content');
        var editingRaw = false;
        var rawTextarea = null;

        toggleBtn.addEventListener('click', function() {
            if (!editingRaw) {
                // switch to raw HTML
                rawTextarea = document.createElement('textarea');
                rawTextarea.style.width = '100%';
                rawTextarea.style.minHeight = '200px';
                rawTextarea.value = editorArea.innerHTML;
                editorArea.parentNode.replaceChild(rawTextarea, editorArea);
                editingRaw = true;
                toggleBtn.textContent = 'WYSIWYG';
            } else {
                // switch back to WYSIWYG
                var newDiv = document.createElement('div');
                newDiv.id = 'post-content';
                newDiv.contentEditable = 'true';
                newDiv.innerHTML = rawTextarea.value;
                rawTextarea.parentNode.replaceChild(newDiv, rawTextarea);
                editorArea = newDiv;
                rawTextarea = null;
                editingRaw = false;
                toggleBtn.textContent = 'HTML';
            }
        });

        document.getElementById('save-post').addEventListener('click', function() {
            var id = <?= $id ?>;
            var title = document.getElementById('post-title').innerText.trim();
            var content;
            if (editingRaw && rawTextarea) {
                content = rawTextarea.value;
            } else {
                content = editorArea.innerHTML;
            }

            var status = document.getElementById('save-status');
            status.textContent = 'Saving...';

            var csrf = <?= json_encode($csrf) ?>;

            fetch('/blog/save', {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': csrf
                },
                body: JSON.stringify({
                    id: id,
                    title: title,
                    content: content
                })
            }).then(function(res) {
                return res.json();
            }).then(function(data) {
                if (data && data.success) {
                    status.textContent = 'Saved';
                    setTimeout(function() {
                        status.textContent = '';
                    }, 2000);
                } else {
                    status.textContent = 'Error: ' + (data.message || 'unknown');
                }
            }).catch(function(err) {
                status.textContent = 'Error';
                console.error(err);
            });
        });
    })();
</script>