<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePatientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('study_id');
            $table->unsignedBigInteger('site_id');
            $table->char('initials', 4);
            $table->date('dob');
            $table->char('gender', 6);
            $table->string('race', 50);
            $table->boolean('icf');
            $table->date('icf_date');
            $table->integer('status')->default(0);
            $table->unsignedBigInteger('created_by'); // fk to users
            $table->unsignedBigInteger('updated_by'); // fk to users, new
            $table->softDeletes();
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
        Schema::dropIfExists('patients');
    }
}
