<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateFileupload extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fileupload', function (Blueprint $table) {
            $table->unsignedBigInteger('study_id')->nullable();
        });

        Schema::create('fileupload_change', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('fileupload_id');
            $table->unsignedInteger('study_id');
            $table->unsignedInteger('visit_id');
            $table->unsignedInteger('patient_id');
            $table->unsignedInteger('updated_by');
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
        Schema::table('fileupload', function (Blueprint $table) {
            $table->dropColumn(['study_id']);
        });
        Schema::dropIfExists('fileupload_change');
    }
}
