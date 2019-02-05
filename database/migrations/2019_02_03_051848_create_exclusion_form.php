<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExclusionForm extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crf_exclusions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('site_id');
            $table->integer('study_id');
            $table->integer('patient_id')->unique();
            $table->date('dov');
            $table->boolean('exclusion');
            $table->string('reason')->nullable();
            $table->boolean('isUpdated')->default(false);
            $table->boolean('hasQuestion')->default(false);
            $table->integer('created_by');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('crf_exclusion');
    }
}
