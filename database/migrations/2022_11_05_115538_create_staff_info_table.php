<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStaffInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staff_info', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('staff_id')->nullable();
            $table->enum('type_of_worker',['employee','contractor'])->nullable();
            $table->enum('type_of_employee',['full_time','part_time'])->nullable();
            $table->enum('type_of_contractor',['individual','business'])->nullable();
            $table->string('business_name')->nullable();
            $table->string('start_date')->nullable();
            $table->string('state_working_in')->nullable();
            $table->enum('pay_rate_type',['per_hour','salary'])->nullable();
            $table->string('pay_rate_amount')->nullable();
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
        Schema::dropIfExists('staff_info');
    }
}
