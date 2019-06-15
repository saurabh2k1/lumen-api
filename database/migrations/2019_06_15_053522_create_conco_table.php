<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConcoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('concos', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('_id');
            $table->unsignedInteger('patient_id');
            $table->string('drugName')->nullable();
            $table->string('indication')->nullable();
            $table->string('eye')->nullable();
            $table->string('route', 10);
            $table->string('dose', 10);
            $table->date('startDate');
            $table->boolean('isongoing');
            $table->date('enddate')->nullable();
            $table->unsignedInteger('created_by');
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
        Schema::dropIfExists('conco');
    }
}
