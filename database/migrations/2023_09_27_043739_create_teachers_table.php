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
        Schema::create('teachers', function (Blueprint $table) {
            $table->id("teacher_id");
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('organisation_id')->nullable()
                ->references('id')
                ->on('organisations')->onDelete('cascade');
            $table->float('allocated_storage_limit');
            $table->foreignId('curr_subscription_id')->nullable()
                ->references('id')
                ->on('subscriptions')->onDelete('cascade');
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
        Schema::dropIfExists('teachers');
    }
};
