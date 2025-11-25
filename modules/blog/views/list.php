<?php

use Modules\Blog\Models\Post;

$isAdmin = \Core\Auth::check();
$recent = array_slice(Post::all(), 0, 5);
?>
<style>
    /* Page layout and blog styles */
    .top-menu {
        background: linear-gradient(180deg, #ffffff, #fbfbfb);
        border-bottom: 1px solid #e6e6e6;
        padding: 10px 20px;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial;
        display: flex;
        align-items: center;
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

    .blog-list {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial;
    }

    .blog-list a {
        color: #0078d4;
        text-decoration: none;
        display: block;
        padding: 10px 0;
        border-bottom: 1px solid #f6f6f6
    }

    .blog-list a:hover {
        text-decoration: underline
    }

    .sidebar h3 {
        margin-top: 0;
        font-size: 16px
    }

    .recent-post {
        padding: 8px 0
    }

    @media (max-width: 800px) {
        .page-wrap {
            flex-direction: column;
            padding: 0 12px
        }

        .sidebar {
            width: 100%;
            order: 2
        }
    }

    /* Simple list tweaks */
    .blog-list {
        max-width: 800px;
        margin: 20px auto;
    }

    .blog-list a {
        padding: 8px 0;
        border-bottom: 1px solid #f0f0f0
    }
</style>

<header class="top-menu">
    <a href="/">Home</a>
    <a href="/blog">Blog</a>
    <a href="/gallery">Gallery</a>
    <?php if ($isAdmin): ?>
        <a href="/admin/users">Admin</a>
        <a href="/admin/logout">Logout</a>
    <?php else: ?>
        <a href="/admin/login?redirect=/blog">Admin Login</a>
    <?php endif; ?>
</header>

<div class="page-wrap">
    <main class="main">
        <div class="blog-list">
            <h1>Blog Posts</h1>

            <?php foreach ($posts as $post): ?>
                <a href="/blog/<?= $post['id'] ?>"><?= htmlspecialchars($post['title']) ?></a>
            <?php endforeach; ?>
        </div>
    </main>

    <aside class="sidebar">
        <h3>Recent Posts</h3>
        <?php foreach ($recent as $r): ?>
            <div class="recent-post"><a href="/blog/<?= $r['id'] ?>"><?= htmlspecialchars($r['title']) ?></a></div>
        <?php endforeach; ?>
    </aside>
</div>