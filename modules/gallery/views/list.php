<?php
// expects $images array
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Gallery</title>
    <style>
        body {
            font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial;
            margin: 0;
            background: #fafafa;
            color: #111
        }

        .wrap {
            max-width: 1100px;
            margin: 28px auto;
            padding: 16px
        }

        .top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 18px
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 12px
        }

        .card {
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 6px 18px rgba(12, 18, 30, .06)
        }

        .card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            display: block
        }

        .caption {
            padding: 8px 10px;
            font-size: 14px
        }

        a.view {
            display: block;
            text-decoration: none;
            color: inherit
        }
    </style>
</head>

<body>
    <div class="wrap">
        <div class="top">
            <h1>Gallery</h1>
            <div><a href="/">Home</a> • <a href="/admin/posts/new">New Post</a></div>
        </div>

        <div class="grid">
            <?php foreach ($images as $img): ?>
                <div class="card">
                    <a class="view" href="/gallery/<?php echo htmlspecialchars($img['id']); ?>">
                        <img src="<?php echo htmlspecialchars($img['thumb'] ?? $img['url']); ?>" alt="<?php echo htmlspecialchars($img['title'] ?? ''); ?>">
                        <div class="caption"><?php echo htmlspecialchars($img['title'] ?? ''); ?></div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>

</html>