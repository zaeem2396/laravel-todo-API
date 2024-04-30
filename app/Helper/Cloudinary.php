<?php

namespace App\Helper;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary as Cloud;

class Cloudinary 
{
    /**
     * Upload image to Cloudinary.
     *
     * @return string The secure URL of uploaded image.
     */

    public static function fileUpload($image)
    {
        $uploadedFile = Cloud::upload($image->getRealPath(), [
            'folder' => env('CLOUDINARY_UPLOAD_FOLDER')
        ]);
        return $uploadedFile->getSecurePath();
    } 
}