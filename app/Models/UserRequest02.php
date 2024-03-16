<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRequest02 extends Model
{
    use HasFactory;

    protected $fillable = [
        'body02',
        'gpt_response02',
        'user_id',
    ];

    protected $table = 'user_request02s';
}
