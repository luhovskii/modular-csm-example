<?php
namespace Modules\Gallery\Controllers;

use Modules\Gallery\Models\Image;

class GalleryController
{
    public function index()
    {
        // get a random selection of images
        $images = Image::random(9);
        include __DIR__ . '/../views/list.php';
    }

    public function show($id)
    {
        $image = Image::find($id);
        if (! $image) {
            http_response_code(404);
            echo "Image not found";
            return;
        }
        include __DIR__ . '/../views/show.php';
    }
}
