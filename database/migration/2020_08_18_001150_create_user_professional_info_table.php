<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserProfessionalInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_professional_info', function (Blueprint $table) {
            $table->id();
            $table->string('userid');
            $table->string('designation');
            $table->string('company');
            $table->mediumText('about_me');
            $table->string('categories_of_interest')->nullable();
            $table->string('subcategories_of_interest')->nullable();
            $table->string('business_category')->nullable();
            $table->string('subcategory')->nullable();
            $table->string('convenient_timings')->nullable();
            $table->timestamps();
            $table->foreign('userid')->references('userid')->on('users')->constrained()
              ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_professional_info');
    }
}
