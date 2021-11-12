<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('image')->nullable();
            $table->string('qr_image')->nullable();
            $table->string('phone')->unique();
            $table->string('password');
            $table->string('api_token')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->string('device_id');
            $table->string('fcm_token');
            $table->integer('verified')->default(1);
            $table->string('code')->nullable();
            $table->enum('type',['admin','student','assistant'])->nullable()->default('student');
            $table->enum('status',['disable','enable'])->default('enable');
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
        Schema::dropIfExists('users');
    }
}
