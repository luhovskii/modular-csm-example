<?php
namespace Modules\Gallery\Models;

class Image
{
    private static function dataFile(): string
    {
        return __DIR__ . '/../data/images.json';
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

    public static function all(): array
    {
        return self::readData();
    }

    public static function find($id)
    {
        $items = self::readData();
        foreach ($items as $item) {
            if (isset($item['id']) && (string)$item['id'] === (string)$id) {
                return $item;
            }
        }
        return null;
    }

    public static function random(int $count = 6): array
    {
        $items = self::readData();
        if (count($items) <= $count) {
            return $items;
        }
        shuffle($items);
        return array_slice($items, 0, $count);
    }
}
