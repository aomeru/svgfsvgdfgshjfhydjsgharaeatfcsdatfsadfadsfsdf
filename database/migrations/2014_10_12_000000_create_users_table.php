<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->increments('id');
            $table->string('staff_id',30)->unique()->nullable();
            $table->string('email',100)->unique();
            $table->string('password')->nullable();
            $table->string('firstname');
            $table->string('lastname');
            $table->string('job_title')->nullable();
            $table->string('city')->default('Lagos');
            $table->string('state')->default('Lagos');
            $table->date('date_of_hire')->nullable();
            $t->enum('employee_type', ['Full Time','Contract','Part Time', 'Graduate Trainee'])->default('Full Time');
            $table->longText('photo')->nullable();
            $table->integer('unit_id')->nullable()->unsigned();
            $table->foreign('unit_id')->references('id')->on('units');
            $table->rememberToken();
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
