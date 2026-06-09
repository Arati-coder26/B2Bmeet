<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserSpecificPermissionsByIdTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_specific_permissions_by_id', function (Blueprint $table) {
            $table->id();
            $table->string('userid');
            $table->mediumText('permissions_granted');
            $table->string('created_by');
            $table->string('updated_by');
            $table->timestamps();
            $table->foreign('userid')->references('userid')->on('users')->constrained()
              ->onDelete('cascade');
            ;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_specific_permissions_by_id');
    }
}
