<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('announcement_to_b2c', function (Blueprint $table) {
            $table->id();
            $table->foreignId('announcement_id')
                ->references('id')
                ->on('announcements')->onDelete('cascade');
            $table->foreignId('to_b2c_id')
            ->references('b2c_user_id')
            ->on('b2c_users')->onDelete('cascade');
            $table->boolean('is_cleared');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('announcement_to_b2c');
    }
};
