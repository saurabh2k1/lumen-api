<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crf_forms', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('form_id');
            $table->integer('field_id')->unsigned();
            $table->string('field_code');
            $table->string('field_title');
            $table->string('field_type');
            $table->string('field_value')->nullable();
            $table->boolean('field_required');
            $table->boolean('field_disabled')->default(false);
            $table->boolean('isEditable')->default(false);
            $table->boolean('hasOption')->nullable();
            $table->string('ngShow_field')->nullable();
            $table->string('ngShow_value')->nullable();
            $table->string('min')->nullable();
            $table->string('max')->nullable();
            $table->string('regex')->nullable();
            $table->string('field_unit')->nullable();

            $table->timestamps();
        });

        Schema::create('crf_field_options', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('field_id')->unsigned();
            $table->integer('option_id')->unsigned();
            $table->string('option_title');
            $table->string('option_value')->nullable();
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
        Schema::dropIfExists('crf_forms');
        Schema::dropIfExists('crf_field_options');
    }
}
