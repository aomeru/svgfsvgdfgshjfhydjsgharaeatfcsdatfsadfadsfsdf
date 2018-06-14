<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function(Blueprint $t){
            $t->string('job_title')->nullable()->after('lastname');
            $t->string('city')->nullable()->default('Lagos')->after('job_title');
            $t->string('state')->nullable()->default('Lagos')->after('city');
            $t->enum('employee_type', ['Full Time','Contract','Part Time', 'Graduate Trainee'])->nullable()->default('Full Time')->after('state');
            $t->longText('photo')->nullable()->after('employee_type');

            $t->integer('unit_id')->nullable()->unsigned()->after('photo');
            $t->foreign('unit_id')->references('id')->on('units');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
