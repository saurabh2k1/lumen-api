<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('studies', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('status')->default(0);
            $table->unsignedBigInteger('created_by'); // fk to users
            $table->unsignedBigInteger('updated_by'); // fk to users, new
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('site_study', function (Blueprint $table) {
            $table->unsignedBigInteger('site_id');
            $table->unsignedBigInteger('study_id');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by');
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
        Schema::dropIfExists('studies');
        Schema::dropIfExists('site_study');
    }
}
