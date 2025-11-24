<?php

namespace Modules\Blog\Controllers;

use Core\View;
use Modules\Blog\Models\Post;

class BlogController
{
    public function index()
    {
        $posts = Post::all();
        return View::render('blog/list', ['posts' => $posts]);
    }

    public function show($id)
    {
        $post = Post::find($id);
        $csrf = \Core\Csrf::getToken();
        return View::render('blog/show', ['post' => $post, 'csrf' => $csrf]);
    }

    public function save()
    {

        // Read JSON body
        $body = file_get_contents('php://input');
        $data = json_decode($body, true);

        header('Content-Type: application/json');

        \Core\Middleware::requireAuthJson();
        \Core\Middleware::requireCsrfJsonOrFail();

        if (! $data || ! isset($data['id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid payload']);
            return;
        }

        $id = (int) $data['id'];
        $title = $data['title'] ?? null;
        $content = $data['content'] ?? null;

        $post = Post::find($id);
        if (! $post) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Post not found']);
            return;
        }

        $updated = [];
        if ($title !== null) {
            $updated['title'] = $title;
        }
        if ($content !== null) {
            $updated['content'] = $content;
        }

        $ok = Post::update($id, $updated);

        if ($ok) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to save']);
        }
    }
}
