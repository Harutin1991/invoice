<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchoolYearReferenceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('school_year_reference', function (Blueprint $table) {
            $table->integer('id',true,true);
            $table->integer('school_id')->index()->unsigned();
            $table->integer('school_year_id')->index()->unsigned();
            $table->integer('allocation_id')->index()->unsigned();
            $table->integer('school_year_param_id')->index()->unsigned()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('school_year_reference');
    }
}
