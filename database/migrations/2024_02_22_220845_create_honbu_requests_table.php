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
        Schema::create('honbu_requests', function (Blueprint $table) {
            $table->id();
        //本部の部門が特性を登録するスペース----------------------------
            $table->text('honbubody');
        //------------------------------------------------------------    
            $table->timestamps();
        
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('honbu_requests');
    }
};
