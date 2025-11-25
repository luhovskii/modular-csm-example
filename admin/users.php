<?php
// Expects $users array and $csrf token
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Admin - Users</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial;
            background: #f7f7f9;
            color: #222;
            margin: 0
        }

        .container {
            max-width: 1000px;
            margin: 28px auto;
            padding: 0 18px
        }

        .top-menu {
            background: linear-gradient(180deg, #ffffff, #fbfbfb);
            border-bottom: 1px solid #e6e6e6;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between
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

        .top-menu .nav {
            display: flex;
            align-items: center
        }

        .top-menu .logout {
            font-size: 13px
        }

        .top-menu .logout a {
            color: #333
        }

        h1 {
            margin: 0;
            font-size: 20px
        }

        .card {
            background: #fff;
            border: 1px solid #e8e8ea;
            border-radius: 8px;
            padding: 16px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03)
        }

        .layout {
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 20px
        }

        table {
            width: 100%;
            border-collapse: collapse
        }

        th,
        td {
            text-align: left;
            padding: 10px 12px;
            border-bottom: 1px solid #f0f0f0
        }

        th {
            background: #fafafa;
            font-weight: 600;
            font-size: 13px
        }

        .muted {
            color: #666;
            font-size: 13px
        }

        .actions form {
            display: inline-block;
            margin-left: 6px
        }

        button,
        .btn {
            display: inline-block;
            padding: 8px 10px;
            border-radius: 6px;
            border: 1px solid #d0d0d4;
            background: #fff;
            cursor: pointer
        }

        button.primary {
            background: #0078d4;
            color: #fff;
            border-color: #0074c8
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 8px 10px;
            border: 1px solid #e0e0e3;
            border-radius: 6px
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px
        }

        .form-row {
            margin-bottom: 12px
        }

        .logout {
            font-size: 13px
        }

        @media (max-width:900px) {
            .layout {
                grid-template-columns: 1fr
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <header class="top-menu">
            <div class="nav">
                <a href="/">Home</a>
                <a href="/blog">Blog</a>
                <a href="/admin/users">Users</a>
                <a href="/admin/posts/new">New Post</a>
            </div>
            <div class="logout"><a href="/admin/logout">Logout</a></div>
        </header>

        <div class="layout">
            <div class="card">
                <h3>Existing Users</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Created</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $username => $meta): ?>
                            <tr>
                                <td><?= htmlspecialchars($username) ?></td>
                                <td class="muted"><?= isset($meta['created']) ? htmlspecialchars($meta['created']) : '&mdash;' ?></td>
                                <td class="actions">
                                    <form method="post" action="/admin/users/delete">
                                        <input type="hidden" name="username" value="<?= htmlspecialchars($username) ?>" />
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>" />
                                        <button type="submit" onclick="return confirm('Delete user <?= htmlspecialchars($username) ?>?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="card">
                <h3>Add User</h3>
                <form method="post" action="/admin/users">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>" />
                    <div class="form-row">
                        <label>Username
                            <input name="username" required />
                        </label>
                    </div>
                    <div class="form-row">
                        <label>Password
                            <input name="password" type="password" required />
                        </label>
                    </div>
                    <div>
                        <button class="btn primary" type="submit">Add User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>