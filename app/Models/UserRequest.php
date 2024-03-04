<?php
    //これは従業員が自分の得意なことを登録するためのモデルファイル

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRequest extends Model
{
    use HasFactory;
    //データベースへ登録する処理--------------------------------------
    protected $fillable = [
        'body',
        'gpt_response',
        'user_id',
    ];
    //--------------------------------------------------------------


}
