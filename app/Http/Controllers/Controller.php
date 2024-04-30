<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
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
}
