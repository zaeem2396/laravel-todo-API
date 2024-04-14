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
        // var_dump($data['errorlog']);exit;
        // foreach($data['errorlog'] as $log) {
        //     echo "<pre>";
        //     $err = json_decode($log->error, true);
        //     print_r($err['http_request']);
        // }exit;
        return view('errorLog', $data);
    }
}
