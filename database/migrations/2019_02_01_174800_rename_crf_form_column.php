<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameCrfFormColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('crf_field_options', function(Blueprint $table){
            $table->renameColumn('field_id', 'crf_form_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('crf_field_options', function(Blueprint $table){
            $table->renameColumn('crf_form_id', 'field_id');
        });
    }
}
