<?php

namespace App\Models;

use App\Helper\DateTime;
use App\Helper\TodoResponse;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;

class Attachments extends Model
{
    use HasFactory;

    protected $table = 'attachments';

    protected $primaryKey = 'id';

    protected $fillable = [
        'task_id',
        'filename',
        'path'
    ];

    public static function uploadAttachments($task_id, $filename, $path)
    {
        try {
            $attachments = self::create([
                'task_id' => $task_id,
                'filename' => $filename,
                'path' => $path
            ]);
        } catch (\Exception $e) {
            TodoResponse::errorLog($_SERVER['REQUEST_METHOD'], URL::full(), [$task_id, $filename, $path], __FILE__, $e->getLine(), __METHOD__, $e->getMessage(), DateTime::formatDateTime());
            TodoResponse::error('System error occured', 500);
        }
    }
}
