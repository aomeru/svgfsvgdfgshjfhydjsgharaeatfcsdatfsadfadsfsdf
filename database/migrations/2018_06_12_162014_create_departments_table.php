<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDepartmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title',150)->unique();

            $table->integer('ed_id')->unsigned()->nullable();
            $table->foreign('ed_id')->references('id')->on('users')->onDelete('cascade');

            $table->integer('gm_id')->unsigned()->nullable();
            $table->foreign('gm_id')->references('id')->on('users')->onDelete('cascade');

            $table->timestamps();
        });

        DB::update("ALTER TABLE departments AUTO_INCREMENT = 64589");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('departments');
    }
}
