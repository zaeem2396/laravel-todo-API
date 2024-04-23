<?php

namespace App\Models;

use App\Helper\DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use App\Helper\TodoResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

class Users extends Model
{
    use HasFactory;

    protected $table = 'users';

    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'email',
        'password'
    ];

    protected $hidden = [
        'password'
    ];

    public function setPasswordAttribute($val)
    {
        if (!empty($val)) {
            $this->attributes['password'] = Hash::make($val);
        }
    }

    public static function createUser(array $data)
    {
        try {
            $existingUser = self::where('email', $data['email'])->first();

            if ($existingUser) {
                TodoResponse::error('User with this email already exists', 409);
            }
            $user = self::create($data);
            if ($user) {
                TodoResponse::success('User created successfully', 200);
            } else {
                TodoResponse::error('Something went wrong', 500);
            }
        } catch (\Exception $e) {
            TodoResponse::errorLog($_SERVER['REQUEST_METHOD'], URL::full(), $data, __FILE__, $e->getLine(), __METHOD__, $e->getMessage(), DateTime::formatDateTime());
            TodoResponse::error('System error occured', 500);
        }
    }

    public static function updateUser(array $data)
    {
        try {
            $user = self::where('id', $data['id'])->first();
            if (!$user) {
                TodoResponse::error('User not found', 404);
            }
            $user->update($data);
            if ($user) {
                TodoResponse::success('User updated successfully', 200);
            } else {
                TodoResponse::error('Something went wrong', 500);
            }
        } catch (\Exception $e) {
            TodoResponse::errorLog($_SERVER['REQUEST_METHOD'], URL::full(), $data, __FILE__, $e->getLine(), __METHOD__, $e->getMessage(), DateTime::formatDateTime());
            TodoResponse::error('System error occured', 500);
        }
    }
}
