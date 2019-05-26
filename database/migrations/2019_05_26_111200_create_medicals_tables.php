<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMedicalsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::create('genmedicals', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('_id');
            $table->unsignedInteger('medicalhistory_id');
            $table->string('indication');
            $table->date('diagnosisDate');
            $table->date('endDate')->nullable();
            $table->boolean('isongoing');
            $table->char('treatment', 2);
            $table->string('description')->nullable();
            $table->timestamps();

        });
        Schema::create('opmedicals', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('_id');
            $table->unsignedInteger('medicalhistory_id');
            $table->string('indication');
            $table->char('eye', 8);
            $table->date('startDate');
            $table->date('endDate')->nullable();
            $table->boolean('isongoing');
            $table->char('treatment', 2);
            $table->string('description')->nullable();
            $table->timestamps();
        });
        Schema::create('medicals', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('_id');
            $table->unsignedInteger('medicalhistory_id');
            $table->string('drugName');
            $table->string('indication');
            $table->char('eye', 8);
            $table->char('route',16);
            $table->char('dose', 16);
            $table->date('startDate');
            $table->date('endDate')->nullable();
            $table->boolean('isongoing');
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
        Schema::dropIfExists('genmedicals');
        Schema::dropIfExists('opmedicals');
        Schema::dropIfExists('medicals');
    }
}
