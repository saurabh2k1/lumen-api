<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVisitTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visits', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('_id')->unique();
            $table->unsignedBigInteger('study_id');
            $table->string('code');
            $table->string('description')->nullable();
            $table->unsignedInteger('min_days');
            $table->unsignedInteger('max_days');
            $table->boolean('is_repeating')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by'); // fk to users
            $table->unsignedBigInteger('updated_by'); // fk to users, new
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
        Schema::dropIfExists('visits');
    }
}
