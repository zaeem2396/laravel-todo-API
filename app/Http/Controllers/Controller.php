<?php

namespace App\Http\Controllers;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Info(title="Laravel-TODO App", version="0.1")
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public static function index()
    {
        $data['errorlog'] = DB::table('error_logs')->orderBy('id', 'desc')->get();
        return view('errorLog', $data);
    }

    public static function file(Request $request)
    {
        $file = $request->file('file');
        $uploadedFile = Cloudinary::upload($file->getRealPath(), [
            'folder' => 'laravel-todo-app'
        ]);
        return $uploadedFile;
    }
}
