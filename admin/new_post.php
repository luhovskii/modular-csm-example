<?php
// Admin create new post form
// expects $csrf variable to be set
if (!isset($csrf)) {
    $csrf = \Core\Csrf::getToken();
}
?>
<!doctype html>
<html lang="en"><head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>New Post</title>
  <style>
    :root{font-family:system-ui,-apple-system,Segoe UI,Roboto,'Helvetica Neue',Arial}
    body{margin:0;background:#f7fafc;color:#111}
    .wrap{max-width:900px;margin:32px auto;padding:20px}
    .top-menu{margin-bottom:18px}
    .card{background:#fff;border-radius:8px;padding:18px;box-shadow:0 6px 18px rgba(12,18,30,.06)}
    h1{margin:0 0 12px}
    label{display:block;margin:12px 0 6px;font-size:14px}
    input[type=text]{width:100%;padding:10px;border:1px solid #e6eef7;border-radius:6px}
    textarea{width:100%;min-height:240px;padding:10px;border:1px solid #e6eef7;border-radius:6px}
    .actions{margin-top:12px;display:flex;gap:8px;align-items:center}
    button{background:#0b61ff;color:#fff;border:none;padding:10px 14px;border-radius:6px;font-weight:600}
    a.btn{display:inline-block;padding:10px 12px;border-radius:6px;border:1px solid #e6eef7;color:#333;text-decoration:none}
  </style>
</head><body>
  <div class="wrap">
    <div class="top-menu"><a href="/admin/users">Users</a> | <a href="/blog">View blog</a> | <a href="/gallery">Gallery</a> | <a href="/admin/logout">Logout</a></div>
    <div class="card">
      <h1>Create New Post</h1>
      <form method="POST" action="/admin/posts">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8'); ?>">
        <label for="title">Title</label>
        <input id="title" name="title" type="text" required>
        <label for="content">Content</label>
        <textarea id="content" name="content"></textarea>
        <div class="actions">
          <button type="submit">Publish</button>
          <a class="btn" href="/admin/users">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</body></html>
