<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExamQuestionAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exam_question_answers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type',['text','image'])->default('text');
            $table->integer('correct')->default(0);
            $table->bigInteger('exam_question_id')->unsigned();
            $table->foreign('exam_question_id')->references('id')->on('exam_questions')->onDelete('restrict');
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
        Schema::dropIfExists('exam_question_answers');
    }
}
