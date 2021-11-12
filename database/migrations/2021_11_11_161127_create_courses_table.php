<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar');
            $table->string('name_en');
            $table->float('price');
            $table->float('discount');
            $table->string('image')->nullable();
            $table->integer('sort')->default(1);
            $table->integer('show')->default(1);
            $table->bigInteger('level_id')->unsigned();
            $table->foreign('level_id')->references('id')->on('levels')->onDelete('restrict');
            $table->bigInteger('instructor_id')->unsigned()->nullable();
            $table->foreign('instructor_id')->references('id')->on('instructors')->onDelete('set null');
            $table->bigInteger('currency_id')->unsigned();
            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('restrict');
            $table->string('desc_ar');
            $table->string('desc_en');
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
        Schema::dropIfExists('courses');
    }
}
