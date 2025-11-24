<?php

namespace Modules\Blog\Models;

class Post
{
    private static function dataFile(): string
    {
        return __DIR__ . '/../data/posts.json';
    }

    private static function readData(): array
    {
        $file = self::dataFile();
        if (! file_exists($file)) {
            return [];
        }

        $content = file_get_contents($file);
        $data = json_decode($content, true);
        return is_array($data) ? $data : [];
    }

    private static function writeData(array $data): bool
    {
        $file = self::dataFile();
        $dir = dirname($file);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        return file_put_contents($file, $json) !== false;
    }

    public static function all()
    {
        return array_values(self::readData());
    }

    public static function find($id)
    {
        $posts = self::readData();
        foreach ($posts as $post) {
            if (isset($post['id']) && (int)$post['id'] === (int)$id) {
                return $post;
            }
        }
        return null;
    }

    public static function update($id, array $fields): bool
    {
        $posts = self::readData();
        $updated = false;
        foreach ($posts as &$post) {
            if (isset($post['id']) && (int)$post['id'] === (int)$id) {
                // Sanitize incoming fields before merging
                $clean = [];
                if (isset($fields['title'])) {
                    // strip tags from title
                    $clean['title'] = trim(strip_tags($fields['title']));
                }
                if (isset($fields['content'])) {
                    $clean['content'] = self::sanitizeContent($fields['content']);
                }

                $post = array_merge($post, $clean);
                $updated = true;
                break;
            }
        }

        if ($updated) {
            return self::writeData($posts);
        }

        return false;
    }

    private static function sanitizeContent(string $html): string
    {
        // Allow a limited set of tags and sanitize attributes on <a>
        $allowed = '<p><br><strong><em><ul><ol><li><a>';
        $stripped = strip_tags($html, $allowed);

        // Use DOMDocument to clean attributes (remove javascript: hrefs etc.)
        libxml_use_internal_errors(true);
        $doc = new \DOMDocument();
        // Wrap in a container to preserve fragments
        $doc->loadHTML('<?xml encoding="utf-8"?><div>' . $stripped . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        foreach ($doc->getElementsByTagName('script') as $n) {
            $n->parentNode->removeChild($n);
        }

        // Sanitize <a> hrefs and remove other attributes
        $anchors = $doc->getElementsByTagName('a');
        for ($i = $anchors->length - 1; $i >= 0; $i--) {
            $a = $anchors->item($i);
            $href = $a->getAttribute('href');
            $href = trim($href);
            // allow absolute http/https or root-relative paths
            $valid = false;
            if (filter_var($href, FILTER_VALIDATE_URL)) {
                $valid = preg_match('#^https?://#i', $href);
            } elseif (strpos($href, '/') === 0) {
                $valid = true;
            }

            if (! $valid) {
                // remove the link but keep inner text
                $text = $doc->createTextNode($a->textContent);
                $a->parentNode->replaceChild($text, $a);
            } else {
                // Keep only href attribute, force rel="nofollow" for safety
                $a->removeAttribute('target');
                $a->setAttribute('rel', 'nofollow');
                $a->setAttribute('href', $href);
            }
        }

        // Extract innerHTML of the wrapper div
        $body = $doc->getElementsByTagName('div')->item(0);
        $out = '';
        foreach ($body->childNodes as $child) {
            $out .= $doc->saveHTML($child);
        }

        // Trim and return
        return trim($out);
    }
}
