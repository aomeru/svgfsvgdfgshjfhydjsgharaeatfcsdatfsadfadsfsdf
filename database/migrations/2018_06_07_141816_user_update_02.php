<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserUpdate02 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users',function(Blueprint $t){
            $t->dropColumn('date_of_hire');
        });

        Schema::table('users',function(Blueprint $t){
            $t->date('date_of_hire')->after('job_title');
            $t->date('location')->after('date_of_hire');
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
