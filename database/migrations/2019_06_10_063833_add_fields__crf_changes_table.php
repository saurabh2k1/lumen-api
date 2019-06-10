<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsCrfChangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('crf_changes', function (Blueprint $table) {
            $table->unsignedInteger('visit_id');
            $table->unsignedInteger('patient_id');
            $table->string('reason');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('crf_changes', function (Blueprint $table) {
            $table->dropColumn(['visit_id', 'patient_id', 'reason']);
        });
    }
}
