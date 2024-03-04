<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_requests', function (Blueprint $table) {
            //追加するカラムの情報-----------------------------------
            $table->string('gpt_response')->after('body');
            //-----------------------------------------------------
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_requests', function (Blueprint $table) {
            //追加したカラムを削除する処理--------------------------
            $table->dropColumn('gpt_response');
        });
    }
};
