<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAerecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aerecords', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('_id');
            $table->unsignedInteger('site_id');
            $table->unsignedInteger('patient_id');
            $table->date('VISDAT');
            $table->text('AETERM');
            $table->string('eventName');
            $table->string('otherEventName')->nullable();
            $table->date('AESTDATE');
            $table->boolean('AEONGO');
            $table->date('AEENDAT')->nullable();
            $table->string('AEOUT');
            $table->unsignedSmallInteger('AESEV'); 
            $table->boolean('AESER');
            $table->unsignedSmallInteger('AEACNOTH');
            $table->unsignedSmallInteger('AEREL');
            $table->unsignedSmallInteger('AEACN');
            $table->unsignedSmallInteger('AEDEVREL');
            $table->string('aeSeq')->unique();
            $table->unsignedSmallInteger('SAECLASS')->nullable();
            $table->unsignedInteger('created_by');
            $table->unsignedInteger('updated_by');
           
            $table->softDeletes();
            $table->timestamps();
            $table->index(['site_id', 'patient_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('aerecords');
    }
}
