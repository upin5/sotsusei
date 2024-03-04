<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HonbuRequest extends Model
{
    use HasFactory;
//table　honbu_requestにデータを登録するための処理---------------------------
    protected $fillable = [
        'honbubody',
    ];
//-------------------------------------------------------------------------


}
