<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchoolYearParamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('school_year_params', function (Blueprint $table) {
            $table->integer('id',true,true);
            $table->integer('school_id')->index()->unsigned();
            $table->float('teacher_up_charge_prct')->nullable();
            $table->float('equip_up_charge_prct')->nullable();
            $table->float('material_up_charge_prct')->nullable();
            $table->float('professional_development_prct')->nullable();
            $table->float('admin_charge_prct')->nullable();
            $table->string('school_year_param')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('school_year_params');
    }
}
