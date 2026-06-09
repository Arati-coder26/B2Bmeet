<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVirtualEventSlotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('virtual_event_meetings', function (Blueprint $table) {
            $table->id();
            $table->string('event_id');
            $table->string('meeting_id')->unique();
            $table->string('participant1_userid');
            $table->string('participant2_userid');
            $table->mediumText('notes')->nullable();
            $table->text('meeting_link')->nullable();
            $table->enum('status', ['Pending', 'In Progress','Completed','Cancelled'])->default('Pending');
            $table->dateTime('from_date_time');
            $table->dateTime('to_date_time');
            $table->timestamps();
            $table->foreign('event_id')->references('event_id')->on('virtual_events')->constrained()->onUpdate('cascade')
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
        Schema::dropIfExists('virtual_event_meetings');
    }
}
