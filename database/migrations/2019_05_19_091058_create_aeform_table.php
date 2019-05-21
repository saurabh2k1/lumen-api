<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAeformTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aeforms', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('_id')->unique(); // New
            $table->unsignedInteger('study_id');
            $table->unsignedInteger('site_id');
            $table->unsignedInteger('patient_id');
            $table->char('isEventOccur',4);
            $table->char('isEyeAffected', 4);
            $table->string('eventName');
            $table->string('otherEventName')->nullable();
            $table->string('eventOccurOn');
            $table->string('severity');
            $table->date('startDate');
            $table->text('description');
            $table->string('eventCriteria')->nullable();
            $table->string('causalityIOL');
            $table->string('causalitySurgical');
            $table->char('isDeviceMalfunction', 4);
            $table->string('deviceMalfunction');
            $table->string('otherMalfunction')->nullable();
            $table->char('isongoing', 4) ;
            $table->date('endDate');
            $table->string('actionTaken');
            $table->string('resolution');
            $table->unsignedBigInteger('created_by'); // fk to users
            $table->unsignedBigInteger('updated_by'); // fk to users,
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
        Schema::dropIfExists('aeform');
    }
}
