<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInboxFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inbox_files', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('inbox_id')->unsigned()->nullable();
            $table->foreign('inbox_id')->references('id')->on('inboxes')->onDelete('restrict');
            $table->string('file');
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
        Schema::dropIfExists('inbox_files');
    }
}
