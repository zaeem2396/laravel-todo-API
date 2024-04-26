<?php

namespace App\Models;

use App\Helper\DateTime;
use App\Helper\TodoResponse;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;

class Category extends Model
{
    use HasFactory;

    protected $table = 'categories';

    protected $primaryKey = 'id';

    protected $fillable = ['name'];

    public static function createCategory(array $inputData)
    {
        try {
            $categoryExist = self::where('name', $inputData['name'])->first();
            if ($categoryExist) {
                TodoResponse::error('Category already exist', 409);
            } else {
                $category = self::create(['name' => $inputData['name']]);
                if ($category) {
                    TodoResponse::success('Category created successfully', 200);
                }
            }
        } catch (\Exception $e) {
            TodoResponse::errorLog($_SERVER['REQUEST_METHOD'], URL::full(), $inputData, __FILE__, $e->getLine(), __METHOD__, $e->getMessage(), DateTime::formatDateTime());
            TodoResponse::error('System error occured', 500);
        }
    }
}
