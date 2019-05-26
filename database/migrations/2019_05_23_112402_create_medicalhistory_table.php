<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMedicalhistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('medicalhistory', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('_id');
            $table->integer('study_id');
            $table->integer('site_id');
            $table->integer('patient_id');
            $table->date('visit_date');
            $table->timestamps();
            $table->unique(['study_id', 'site_id', 'patient_id']);
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('medicalhistory');
        
    }
}
