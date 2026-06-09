<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('event_id')->unique();
            $table->enum('type', ['Virtual', 'Physical'])->default('Virtual');            
            $table->enum('status', ['Pending', 'In Progress','Completed','Cancelled'])->default('Pending');
            $table->dateTime('from_date_time');
            $table->dateTime('to_date_time');
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
        Schema::dropIfExists('events');
    }
}
