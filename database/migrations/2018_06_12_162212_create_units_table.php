<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUnitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('units', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title',150)->unique();

            $table->integer('department_id')->unsigned();
            $table->foreign('department_id')->references('id')->on('departments');

            $table->integer('manager_id')->unsigned()->nullable();
            $table->foreign('manager_id')->references('id')->on('users')->onDelete('cascade');

            $table->timestamps();
        });

        DB::update("ALTER TABLE units AUTO_INCREMENT = 64589");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('units');
    }
}
