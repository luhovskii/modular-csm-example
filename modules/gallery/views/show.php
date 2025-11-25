<?php
// expects $image variable
if (!isset($image)) {
    echo 'No image';
    return;
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?php echo htmlspecialchars($image['title'] ?? 'Image'); ?></title>
    <style>
        body {
            font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial;
            margin: 0;
            background: #fff;
            color: #111
        }

        .wrap {
            max-width: 900px;
            margin: 24px auto;
            padding: 16px
        }

        .img-wrap {
            background: #000;
            border-radius: 8px;
            overflow: hidden
        }

        .img-wrap img {
            width: 100%;
            height: auto;
            display: block
        }

        .meta {
            margin-top: 10px;
            color: #666
        }

        a.back {
            display: inline-block;
            margin-bottom: 12px
        }
    </style>
</head>

<body>
    <div class="wrap">
        <a class="back" href="/gallery">← Back to gallery</a>
        <div class="img-wrap">
            <img src="<?php echo htmlspecialchars($image['url']); ?>" alt="<?php echo htmlspecialchars($image['title'] ?? ''); ?>">
        </div>
        <div class="meta">
            <h2><?php echo htmlspecialchars($image['title'] ?? ''); ?></h2>
            <p><?php echo htmlspecialchars($image['description'] ?? ''); ?></p>
        </div>
    </div>
</body>

</html>