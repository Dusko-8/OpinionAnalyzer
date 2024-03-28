<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('comment_topic', function (Blueprint $table) {
            $table->unsignedBigInteger('comment_id');
            $table->unsignedBigInteger('topic_id');
            $table->foreign('comment_id')->references('comment_id')->on('comments')->onDelete('cascade');
            $table->foreign('topic_id')->references('topic_id')->on('topics')->onDelete('cascade');
            $table->primary(['comment_id', 'topic_id']); // Composite primary key
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('comment_topic');
    }
};
