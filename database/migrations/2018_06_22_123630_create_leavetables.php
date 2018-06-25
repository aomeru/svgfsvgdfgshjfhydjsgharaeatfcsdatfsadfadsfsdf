<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeavetables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leave', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('leave_type_id')->unsigned();
            $table->foreign('leave_type_id')->references('id')->on('leave_type');
            $table->integer('rstaff_id')->unsigned();
            $table->foreign('rstaff_id')->references('id')->on('users');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->date('back_on')->nullable();
            $table->date('call_off')->nullable();
            $table->integer('year');
            $table->string('status')->default('pending');
            $table->text('comment');
            $table->timestamps();
        });

        Schema::create('leave_request', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code',50)->unique();
            $table->integer('leave_id')->unsigned();
            $table->foreign('leave_id')->references('id')->on('leave');
            $table->integer('manager_id')->unsigned();
            $table->foreign('manager_id')->references('id')->on('users');
            $table->integer('hr_id')->unsigned();
            $table->foreign('hr_id')->references('id')->on('users');
            $table->enum('manager_decision',['pending','manager_approved','manager_declined','manager_deferred'])->default('pending');
            $table->date('manager_decision_date')->nullable();
            $table->enum('hr_decision',['pending','hr_approved','hr_declined','hr_deferred'])->default('pending');
            $table->date('hr_decision_date')->nullable();
            $table->string('status')->default('requested');
            $table->timestamps();
        });

        Schema::create('leave_request_deference', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('leave_request_id')->unsigned();
            $table->foreign('leave_request_id')->references('id')->on('leave_request');
            $table->enum('type',['manager','hr']);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->date('back_on')->nullable();
            $table->text('comment');
            $table->timestamps();
        });

         Schema::create('leave_request_log', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('leave_request_id')->unsigned();
            $table->foreign('leave_request_id')->references('id')->on('leave_request');
            $table->integer('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->enum('type',['sys','comment'])->default('sys');
            $table->text('comment');
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
        //
    }
}
