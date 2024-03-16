<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class soukatsu extends Model
{
    use HasFactory;
    protected $fillable = [
        'gpt_text',
        'user_id',
    ];
}
